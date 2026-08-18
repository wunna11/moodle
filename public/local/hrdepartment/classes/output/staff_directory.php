<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Renderable for the Staff directory: a searchable/filterable, paginated
 * card grid of every staff member, replacing the old table_sql listing
 * (classes/table/staff_table.php, now unused) with the same card-grid
 * design used by the Lecturers and Students directories. Deliberately
 * simpler than lecturers_directory: no academic pill/text and no course
 * assignment action, since staff have neither (see staff_profile).
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_hrdepartment\output;

defined('MOODLE_INTERNAL') || die();

use local_hrdepartment\department_helper;
use local_hrdepartment\staff_manager;
use local_hrdepartment\user_account_sync;
use moodle_url;
use renderable;
use renderer_base;
use templatable;

/**
 * Class staff_directory
 */
class staff_directory implements renderable, templatable {

    /** @var string */
    protected $search;

    /** @var int */
    protected $departmentid;

    /** @var string */
    protected $status;

    /** @var int zero-based page number */
    protected $page;

    /** @var moodle_url the page's own URL, used as the filter form action and the paging bar base */
    protected $baseurl;

    /** @var int */
    protected $perpage;

    /**
     * Constructor.
     *
     * @param string $search name/email/employee code search, '' = any
     * @param int $departmentid 0 = any department
     * @param string $status '' = any, 'active', or 'suspended'
     * @param int $page zero-based page number
     * @param moodle_url $baseurl
     * @param int $perpage
     */
    public function __construct(
        string $search,
        int $departmentid,
        string $status,
        int $page,
        moodle_url $baseurl,
        int $perpage = staff_manager::PAGE_SIZE
    ) {
        $this->search = $search;
        $this->departmentid = $departmentid;
        $this->status = $status;
        $this->page = $page;
        $this->baseurl = $baseurl;
        $this->perpage = $perpage;
    }

    /**
     * Export this renderable's data for the mustache template.
     *
     * @param renderer_base $output
     * @return array
     */
    public function export_for_template(renderer_base $output): array {
        $stats = staff_manager::get_summary_stats();
        $total = staff_manager::count_staff($this->search, $this->departmentid, $this->status);
        $stafflist = staff_manager::get_staff_list($this->search, $this->departmentid, $this->status, $this->page, $this->perpage);

        $staffdata = [];
        foreach ($stafflist as $staff) {
            // employmentstatus (stored) can drift from the live Moodle
            // account - the same "live state wins" rule used everywhere
            // else in this module (see staff_manager::build_where()).
            $issuspended = user_account_sync::is_account_suspended($staff->id);

            $staffdata[] = [
                'id' => $staff->employeeid,
                'fullname' => $staff->fullname,
                'email' => $staff->email,
                'employeecode' => $staff->employeecode,
                'departmentname' => $staff->departmentname !== null && $staff->departmentname !== ''
                    ? format_string($staff->departmentname)
                    : '',
                'hasdepartment' => $staff->departmentname !== null && $staff->departmentname !== '',
                'designation' => $staff->designation ?: '',
                'hasdesignation' => $staff->designation !== null && $staff->designation !== '',
                'avatarhtml' => $output->user_picture($staff, ['size' => 64, 'link' => false, 'class' => 'rounded-circle']),
                'viewurl' => (new moodle_url('/local/hrdepartment/staff/view.php', ['id' => $staff->employeeid]))->out(false),
                'editurl' => (new moodle_url('/local/hrdepartment/staff/edit.php', ['id' => $staff->employeeid]))->out(false),
                'deactivateurl' => (new moodle_url('/local/hrdepartment/staff/delete.php', ['id' => $staff->employeeid]))->out(false),
                'reactivateurl' => (new moodle_url('/local/hrdepartment/staff/delete.php', [
                    'id' => $staff->employeeid, 'reactivate' => 1,
                ]))->out(false),
                'issuspended' => $issuspended,
            ];
        }

        $departmentoptions = [
            ['value' => 0, 'label' => get_string('alldepartments', 'local_hrdepartment'), 'issel' => $this->departmentid === 0],
        ];
        foreach (department_helper::get_options() as $id => $name) {
            $departmentoptions[] = ['value' => $id, 'label' => $name, 'issel' => $this->departmentid === $id];
        }

        $statusoptions = [
            ['value' => '', 'label' => get_string('allstatuses', 'local_hrdepartment'), 'issel' => $this->status === ''],
            ['value' => 'active', 'label' => get_string('status_active', 'local_hrdepartment'), 'issel' => $this->status === 'active'],
            ['value' => 'suspended', 'label' => get_string('status_suspended', 'local_hrdepartment'), 'issel' => $this->status === 'suspended'],
        ];

        $pagingbarhtml = '';
        if ($total > $this->perpage) {
            $pagingbarhtml = $output->paging_bar($total, $this->page, $this->perpage, $this->baseurl);
        }

        return [
            'totalstaff' => $stats->totalstaff,
            'activestaff' => $stats->activestaff,
            'suspendedstaff' => $stats->suspendedstaff,
            'departmentcount' => $stats->departmentcount,

            'search' => $this->search,
            'formaction' => $this->baseurl->out_omit_querystring(),
            'departmentoptions' => $departmentoptions,
            'statusoptions' => $statusoptions,
            'addstaffurl' => (new moodle_url('/local/hrdepartment/staff/edit.php'))->out(false),

            'hasstaff' => !empty($staffdata),
            'stafflist' => $staffdata,
            'resultcount' => $total,

            'haspaging' => $pagingbarhtml !== '',
            'pagingbarhtml' => $pagingbarhtml,
        ];
    }
}
