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
 * Renderable for the Lecturers directory: a searchable/filterable,
 * paginated card grid of every lecturer, replacing the old table_sql
 * listing (classes/table/lecturers_table.php, now unused) with the same
 * modern card-grid design used by the Students directory.
 *
 * @package   local_hrdepartment
 * @copyright 2026 Wunna
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_hrdepartment\output;

defined('MOODLE_INTERNAL') || die();

use local_hrdepartment\department_helper;
use local_hrdepartment\lecturer_manager;
use local_hrdepartment\user_account_sync;
use moodle_url;
use renderable;
use renderer_base;
use templatable;

/**
 * Class lecturers_directory
 */
class lecturers_directory implements renderable, templatable {

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
        int $perpage = lecturer_manager::PAGE_SIZE
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
        $stats = lecturer_manager::get_summary_stats();
        $total = lecturer_manager::count_lecturers($this->search, $this->departmentid, $this->status);
        $lecturers = lecturer_manager::get_lecturers($this->search, $this->departmentid, $this->status, $this->page, $this->perpage);

        $lecturerdata = [];
        foreach ($lecturers as $lecturer) {
            // employmentstatus (stored) can drift from the live Moodle
            // account - the same "live state wins" rule used everywhere
            // else in this module (see lecturer_manager::build_where()).
            $issuspended = user_account_sync::is_account_suspended($lecturer->id);

            $lecturerdata[] = [
                'id' => $lecturer->employeeid,
                'fullname' => $lecturer->fullname,
                'email' => $lecturer->email,
                'employeecode' => $lecturer->employeecode,
                'departmentname' => $lecturer->departmentname !== null && $lecturer->departmentname !== ''
                    ? format_string($lecturer->departmentname)
                    : '',
                'hasdepartment' => $lecturer->departmentname !== null && $lecturer->departmentname !== '',
                'designation' => $lecturer->designation ?: '',
                'hasdesignation' => $lecturer->designation !== null && $lecturer->designation !== '',
                'qualification' => $lecturer->qualification ?: '',
                'specialization' => $lecturer->specialization ?: '',
                'hasacademic' => !empty($lecturer->qualification) || !empty($lecturer->specialization),
                'avatarhtml' => $output->user_picture($lecturer, ['size' => 64, 'link' => false, 'class' => 'rounded-circle']),
                'viewurl' => (new moodle_url('/local/hrdepartment/lecturer/view.php', ['id' => $lecturer->employeeid]))->out(false),
                'editurl' => (new moodle_url('/local/hrdepartment/lecturer/edit.php', ['id' => $lecturer->employeeid]))->out(false),
                'assignurl' => (new moodle_url('/local/hrdepartment/lecturer/courseassign.php', ['id' => $lecturer->employeeid]))->out(false),
                'deactivateurl' => (new moodle_url('/local/hrdepartment/lecturer/delete.php', ['id' => $lecturer->employeeid]))->out(false),
                'reactivateurl' => (new moodle_url('/local/hrdepartment/lecturer/delete.php', [
                    'id' => $lecturer->employeeid, 'reactivate' => 1,
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
            'totallecturers' => $stats->totallecturers,
            'activelecturers' => $stats->activelecturers,
            'suspendedlecturers' => $stats->suspendedlecturers,
            'departmentcount' => $stats->departmentcount,

            'search' => $this->search,
            'formaction' => $this->baseurl->out_omit_querystring(),
            'departmentoptions' => $departmentoptions,
            'statusoptions' => $statusoptions,
            'addlecturerurl' => (new moodle_url('/local/hrdepartment/lecturer/edit.php'))->out(false),

            'haslecturers' => !empty($lecturerdata),
            'lecturers' => $lecturerdata,
            'resultcount' => $total,

            'haspaging' => $pagingbarhtml !== '',
            'pagingbarhtml' => $pagingbarhtml,
        ];
    }
}
