# Moodle Grade-Related Functions

Total: **2,818 functions** across **779 files**

Generated from all files with "grade" in their path.

---

## Index by Directory

- [grade/](#grade) — Core grade module
- [availability/condition/grade/](#availabilityconditiongrade) — Grade availability conditions
- [course/](#course) — Course grade-related
- [completion/criteria/](#completioncriteria) — Grade completion criteria
- [enrol/lti/](#enrollti) — LTI grade sync
- [Other modules](#other-modules)

---

## Full Function List

- `public/admin/presets/classes/local/setting/adminpresets_admin_setting_gradecat_combo.php:37`
  ```php
  public function __construct(admin_setting $settingdata, $dbsettingvalue) {
  ```
- `public/admin/presets/classes/local/setting/adminpresets_admin_setting_gradecat_combo.php:46`
  ```php
  protected function set_visiblevalue() {
  ```
- `public/admin/tool/cohortroles/db/upgrade.php:31`
  ```php
  function xmldb_tool_cohortroles_upgrade($oldversion) {
  ```
- `public/admin/tool/customlang/db/upgrade.php:26`
  ```php
  function xmldb_tool_customlang_upgrade($oldversion) {
  ```
- `public/admin/tool/dataprivacy/db/upgrade.php:31`
  ```php
  function xmldb_tool_dataprivacy_upgrade($oldversion) {
  ```
- `public/admin/tool/installaddon/db/upgrade.php:31`
  ```php
  function xmldb_tool_installaddon_upgrade(int $oldversion): bool {
  ```
- `public/admin/tool/log/db/upgrade.php:31`
  ```php
  function xmldb_tool_log_upgrade($oldversion) {
  ```
- `public/admin/tool/log/store/database/db/upgrade.php:25`
  ```php
  function xmldb_logstore_database_upgrade($oldversion) {
  ```
- `public/admin/tool/log/store/standard/db/upgrade.php:25`
  ```php
  function xmldb_logstore_standard_upgrade($oldversion) {
  ```
- `public/admin/tool/lp/classes/course_competency_overridegrade_form_element.php:48`
  ```php
  public function __construct($elementname=null, $elementlabel=null, $options=[]) {
  ```
- `public/admin/tool/mfa/factor/auth/db/upgrade.php:30`
  ```php
  function xmldb_factor_auth_upgrade($oldversion) {
  ```
- `public/admin/tool/mfa/factor/email/db/upgrade.php:30`
  ```php
  function xmldb_factor_email_upgrade($oldversion): bool {
  ```
- `public/admin/tool/mfa/factor/sms/db/upgrade.php:31`
  ```php
  function xmldb_factor_sms_upgrade(int $oldversion): bool {
  ```
- `public/admin/tool/mfa/factor/totp/db/upgrade.php:30`
  ```php
  function xmldb_factor_totp_upgrade($oldversion): bool {
  ```
- `public/admin/tool/mobile/db/upgrade.php:31`
  ```php
  function xmldb_tool_mobile_upgrade($oldversion) {
  ```
- `public/admin/tool/monitor/db/upgrade.php:31`
  ```php
  function xmldb_tool_monitor_upgrade($oldversion) {
  ```
- `public/admin/tool/policy/db/upgrade.php:32`
  ```php
  function xmldb_tool_policy_upgrade($oldversion) {
  ```
- `public/admin/tool/recyclebin/db/upgrade.php:31`
  ```php
  function xmldb_tool_recyclebin_upgrade($oldversion) {
  ```
- `public/admin/tool/usertours/db/upgrade.php:34`
  ```php
  function xmldb_tool_usertours_upgrade($oldversion) {
  ```
- `public/auth/db/db/upgrade.php:30`
  ```php
  function xmldb_auth_db_upgrade($oldversion) {
  ```
- `public/auth/email/db/upgrade.php:30`
  ```php
  function xmldb_auth_email_upgrade($oldversion) {
  ```
- `public/auth/ldap/db/upgrade.php:30`
  ```php
  function xmldb_auth_ldap_upgrade($oldversion) {
  ```
- `public/auth/lti/db/upgrade.php:31`
  ```php
  function xmldb_auth_lti_upgrade($oldversion) {
  ```
- `public/auth/manual/db/upgrade.php:30`
  ```php
  function xmldb_auth_manual_upgrade($oldversion) {
  ```
- `public/auth/none/db/upgrade.php:30`
  ```php
  function xmldb_auth_none_upgrade($oldversion) {
  ```
- `public/auth/oauth2/db/upgrade.php:31`
  ```php
  function xmldb_auth_oauth2_upgrade($oldversion) {
  ```
- `public/auth/shibboleth/db/upgrade.php:30`
  ```php
  function xmldb_auth_shibboleth_upgrade($oldversion) {
  ```
- `public/availability/condition/grade/classes/callbacks.php:45`
  ```php
  public static function grade_changed($userid) {
  ```
- `public/availability/condition/grade/classes/callbacks.php:54`
  ```php
  public static function grade_item_changed($courseid) {
  ```
- `public/availability/condition/grade/classes/condition.php:103`
  ```php
  public function is_available($not, \core_availability\info $info, $grabthelot, $userid) {
  ```
- `public/availability/condition/grade/classes/condition.php:116`
  ```php
  public function get_description($full, $not, \core_availability\info $info) {
  ```
- `public/availability/condition/grade/classes/condition.php:151`
  ```php
  public static function get_description_callback_value(
  ```
- `public/availability/condition/grade/classes/condition.php:160`
  ```php
  protected function get_debug_string() {
  ```
- `public/availability/condition/grade/classes/condition.php:185`
  ```php
  private static function get_cached_grade_name($courseid, $gradeitemid) {
  ```
- `public/availability/condition/grade/classes/condition.php:230`
  ```php
  protected static function get_cached_grade_score($gradeitemid, $courseid,
  ```
- `public/availability/condition/grade/classes/condition.php:292`
  ```php
  public function update_after_restore($restoreid, $courseid, \base_logger $logger, $name) {
  ```
- `public/availability/condition/grade/classes/condition.php:313`
  ```php
  public function update_dependency_id($table, $oldid, $newid) {
  ```
- `public/availability/condition/grade/classes/condition.php:45`
  ```php
  public function __construct($structure) {
  ```
- `public/availability/condition/grade/classes/condition.php:70`
  ```php
  public function save() {
  ```
- `public/availability/condition/grade/classes/condition.php:92`
  ```php
  public static function get_json($gradeitemid, $min = null, $max = null) {
  ```
- `public/availability/condition/grade/classes/frontend.php:37`
  ```php
  protected function get_javascript_strings() {
  ```
- `public/availability/condition/grade/classes/frontend.php:41`
  ```php
  protected function get_javascript_init_params($course, ?\cm_info $cm = null,
  ```
- `public/availability/condition/grade/classes/privacy/provider.php:43`
  ```php
  public static function get_reason(): string {
  ```
- `public/availability/condition/grade/tests/condition_test.php:144`
  ```php
  public function test_constructor(): void {
  ```
- `public/availability/condition/grade/tests/condition_test.php:208`
  ```php
  public function test_save(): void {
  ```
- `public/availability/condition/grade/tests/condition_test.php:227`
  ```php
  protected static function set_grade($assignrow, $userid, $grade) {
  ```
- `public/availability/condition/grade/tests/condition_test.php:238`
  ```php
  public function test_update_dependency_id(): void {
  ```
- `public/availability/condition/grade/tests/condition_test.php:30`
  ```php
  public function test_usage(): void {
  ```
- `public/backup/moodle2/tests/restore_gradebook_structure_step_test.php:40`
  ```php
  public static function rewrite_step_backup_file_for_legacy_freeze_provider(): array {
  ```
- `public/backup/moodle2/tests/restore_gradebook_structure_step_test.php:67`
  ```php
  public function test_rewrite_step_backup_file_for_legacy_freeze($source, $expected): void {
  ```
- `public/badges/upgradelib.php:33`
  ```php
  function badges_install_default_backpacks() {
  ```
- `public/blocks/badges/db/upgrade.php:45`
  ```php
  function xmldb_block_badges_upgrade($oldversion, $block) {
  ```
- `public/blocks/calendar_month/db/upgrade.php:45`
  ```php
  function xmldb_block_calendar_month_upgrade($oldversion, $block) {
  ```
- `public/blocks/calendar_upcoming/db/upgrade.php:45`
  ```php
  function xmldb_block_calendar_upcoming_upgrade($oldversion, $block) {
  ```
- `public/blocks/completionstatus/db/upgrade.php:46`
  ```php
  function xmldb_block_completionstatus_upgrade($oldversion, $block) {
  ```
- `public/blocks/configurable_reports/db/upgrade.php:34`
  ```php
  function xmldb_block_configurable_reports_upgrade($oldversion) {
  ```
- `public/blocks/course_summary/db/upgrade.php:46`
  ```php
  function xmldb_block_course_summary_upgrade($oldversion, $block) {
  ```
- `public/blocks/exacomp/classes/event/example_graded.php:34`
  ```php
  protected function init() {
  ```
- `public/blocks/exacomp/classes/event/example_graded.php:45`
  ```php
  public static function get_name() {
  ```
- `public/blocks/exacomp/classes/event/example_graded.php:54`
  ```php
  public function get_description() {
  ```
- `public/blocks/exacomp/classes/event/example_graded.php:63`
  ```php
  public function get_url() {
  ```
- `public/blocks/exacomp/db/upgrade.php:1520`
  ```php
  function upgrade_block_exacomp_2015052900_get_descriptors_by_topic($courseid, $topicid) {
  ```
- `public/blocks/exacomp/db/upgrade.php:1569`
  ```php
  function upgrade_block_exacomp_2015072102_block_exacomp_get_topics_by_course_and_subject($courseid, $subjectid = 0, $showalldescriptors = false) {
  ```
- `public/blocks/exacomp/db/upgrade.php:1590`
  ```php
  function upgrade_block_exacomp_2015072102_get_descriptors($courseid = 0) {
  ```
- `public/blocks/exacomp/db/upgrade.php:1623`
  ```php
  function upgrade_block_exacomp_2015072102_get_child_descriptors($parent, $courseid, $showalldescriptors = false, $filteredtaxonomies = array(BLOCK_EXACOMP_SHOW_ALL_TAXONOMIES), $showallexamples = true, $mindvisibility = true,
  ```
- `public/blocks/exacomp/db/upgrade.php:19`
  ```php
  function xmldb_block_exacomp_upgrade($oldversion) {
  ```
- `public/blocks/exacomp/db/upgrade.php:2131`
  ```php
  function upgrade_block_exacomp_2015082000_get_examples_for_descriptor($descriptor, $filteredtaxonomies = array(BLOCK_EXACOMP_SHOW_ALL_TAXONOMIES), $showallexamples = true, $courseid = null, $mind_visibility = true,
  ```
- `public/blocks/exacomp/db/upgrade.php:2189`
  ```php
  function upgrade_block_exacomp_2015072102_block_exacomp_get_examples_for_descriptor($descriptor, $filteredtaxonomies = array(BLOCK_EXACOMP_SHOW_ALL_TAXONOMIES), $showallexamples = true, $courseid = null, $mind_visibility = true,
  ```
- `public/blocks/exacomp/db/upgrade.php:2310`
  ```php
  function upgrade_block_exacomp_2015082500_move_to_file_storage($item, $type) {
  ```
- `public/blocks/exacomp/db/upgrade.php:2645`
  ```php
  function upgrade_block_exacomp_2016041402_move_to_file_storage($item) {
  ```
- `public/blocks/exacomp/db/upgradelib.php:30`
  ```php
  function block_exacomp_seed_assessment_configurations() {
  ```
- `public/blocks/exastud/db/upgrade.php:22`
  ```php
  function xmldb_block_exastud_upgrade($oldversion = 0) {
  ```
- `public/blocks/feedback/db/upgrade.php:45`
  ```php
  function xmldb_block_feedback_upgrade($oldversion, $block) {
  ```
- `public/blocks/html/db/upgrade.php:31`
  ```php
  function xmldb_block_html_upgrade($oldversion) {
  ```
- `public/blocks/myoverview/db/upgrade.php:31`
  ```php
  function xmldb_block_myoverview_upgrade($oldversion) {
  ```
- `public/blocks/navigation/db/upgrade.php:53`
  ```php
  function xmldb_block_navigation_upgrade($oldversion, $block) {
  ```
- `public/blocks/recent_activity/db/upgrade.php:45`
  ```php
  function xmldb_block_recent_activity_upgrade($oldversion, $block) {
  ```
- `public/blocks/recentlyaccesseditems/db/upgrade.php:45`
  ```php
  function xmldb_block_recentlyaccesseditems_upgrade($oldversion, $block) {
  ```
- `public/blocks/rss_client/db/upgrade.php:31`
  ```php
  function xmldb_block_rss_client_upgrade($oldversion) {
  ```
- `public/blocks/selfcompletion/db/upgrade.php:46`
  ```php
  function xmldb_block_selfcompletion_upgrade($oldversion, $block) {
  ```
- `public/blocks/settings/db/upgrade.php:53`
  ```php
  function xmldb_block_settings_upgrade($oldversion, $block) {
  ```
- `public/blocks/tag_youtube/db/upgrade.php:30`
  ```php
  function xmldb_block_tag_youtube_upgrade($oldversion) {
  ```
- `public/blocks/timeline/db/upgrade.php:45`
  ```php
  function xmldb_block_timeline_upgrade($oldversion, $block) {
  ```
- `public/communication/provider/matrix/db/upgrade.php:30`
  ```php
  function xmldb_communication_matrix_upgrade($oldversion) {
  ```
- `public/completion/criteria/completion_criteria_grade.php:102`
  ```php
  private function get_grade($completion) {
  ```
- `public/completion/criteria/completion_criteria_grade.php:114`
  ```php
  public function review($completion, $mark = true) {
  ```
- `public/completion/criteria/completion_criteria_grade.php:136`
  ```php
  public function get_title() {
  ```
- `public/completion/criteria/completion_criteria_grade.php:145`
  ```php
  public function get_title_detailed() {
  ```
- `public/completion/criteria/completion_criteria_grade.php:158`
  ```php
  public function get_type_title() {
  ```
- `public/completion/criteria/completion_criteria_grade.php:168`
  ```php
  public function get_status($completion) {
  ```
- `public/completion/criteria/completion_criteria_grade.php:188`
  ```php
  public function cron() {
  ```
- `public/completion/criteria/completion_criteria_grade.php:246`
  ```php
  public function get_details($completion) {
  ```
- `public/completion/criteria/completion_criteria_grade.php:272`
  ```php
  public function get_icon($alt, ?array $attributes = null) {
  ```
- `public/completion/criteria/completion_criteria_grade.php:52`
  ```php
  public static function fetch($params) {
  ```
- `public/completion/criteria/completion_criteria_grade.php:63`
  ```php
  public function config_form_display(&$mform, $data = null) {
  ```
- `public/completion/criteria/completion_criteria_grade.php:83`
  ```php
  public function update_config(&$data) {
  ```
- `public/course/classes/analytics/target/course_gradetopass.php:116`
  ```php
  public static function get_name(): \lang_string {
  ```
- `public/course/classes/analytics/target/course_gradetopass.php:125`
  ```php
  protected static function classes_description() {
  ```
- `public/course/classes/analytics/target/course_gradetopass.php:141`
  ```php
  public function is_valid_analysable(\core_analytics\analysable $course, $fortraining = true) {
  ```
- `public/course/classes/analytics/target/course_gradetopass.php:165`
  ```php
  protected function calculate_sample($sampleid, \core_analytics\analysable $course, $starttime = false, $endtime = false) {
  ```
- `public/course/classes/analytics/target/course_gradetopass.php:59`
  ```php
  protected function get_course_gradetopass($courseid) {
  ```
- `public/course/classes/analytics/target/course_gradetopass.php:87`
  ```php
  protected function get_user_grade($courseitemid, $userid) {
  ```
- `public/course/classes/task/regrade_final_grades.php:43`
  ```php
  public static function create(int $courseid): self {
  ```
- `public/course/classes/task/regrade_final_grades.php:56`
  ```php
  public function execute(): void {
  ```
- `public/course/format/topics/db/upgrade.php:31`
  ```php
  function xmldb_format_topics_upgrade($oldversion) {
  ```
- `public/course/format/weeks/db/upgrade.php:31`
  ```php
  function xmldb_format_weeks_upgrade($oldversion) {
  ```
- `public/enrol/database/db/upgrade.php:25`
  ```php
  function xmldb_enrol_database_upgrade($oldversion) {
  ```
- `public/enrol/fee/db/upgrade.php:34`
  ```php
  function xmldb_enrol_fee_upgrade($oldversion) {
  ```
- `public/enrol/flatfile/db/upgrade.php:25`
  ```php
  function xmldb_enrol_flatfile_upgrade($oldversion) {
  ```
- `public/enrol/guest/db/upgrade.php:25`
  ```php
  function xmldb_enrol_guest_upgrade($oldversion) {
  ```
- `public/enrol/imsenterprise/db/upgrade.php:31`
  ```php
  function xmldb_enrol_imsenterprise_upgrade($oldversion) {
  ```
- `public/enrol/lti/classes/local/ltiadvantage/task/sync_grades.php:36`
  ```php
  public function get_name() {
  ```
- `public/enrol/lti/classes/local/ltiadvantage/task/sync_grades.php:45`
  ```php
  public function execute() {
  ```
- `public/enrol/lti/classes/local/ltiadvantage/task/sync_tool_grades.php:217`
  ```php
  protected function get_line_item_label(\stdClass $resource, \context $context): string {
  ```
- `public/enrol/lti/classes/local/ltiadvantage/task/sync_tool_grades.php:248`
  ```php
  protected function get_ags(LtiServiceConnector $sc, LtiRegistration $registration, array $sd): LtiAssignmentsGradesService {
  ```
- `public/enrol/lti/classes/local/ltiadvantage/task/sync_tool_grades.php:257`
  ```php
  public function execute() {
  ```
- `public/enrol/lti/classes/local/ltiadvantage/task/sync_tool_grades.php:48`
  ```php
  protected function sync_grades_for_resource($resource): array {
  ```
- `public/enrol/lti/classes/task/sync_grades.php:41`
  ```php
  public function get_name() {
  ```
- `public/enrol/lti/classes/task/sync_grades.php:50`
  ```php
  public function execute() {
  ```
- `public/enrol/lti/db/upgrade.php:37`
  ```php
  function xmldb_enrol_lti_upgrade($oldversion) {
  ```
- `public/enrol/lti/tests/local/ltiadvantage/task/sync_grades_test.php:43`
  ```php
  public function test_get_name(): void {
  ```
- `public/enrol/lti/tests/local/ltiadvantage/task/sync_grades_test.php:52`
  ```php
  public function test_sync_grades_gradesync_disabled(): void {
  ```
- `public/enrol/lti/tests/local/ltiadvantage/task/sync_grades_test.php:73`
  ```php
  public function test_sync_grades_auth_plugin_disabled(): void {
  ```
- `public/enrol/lti/tests/local/ltiadvantage/task/sync_grades_test.php:94`
  ```php
  public function test_sync_grades_enrol_plugin_disabled(): void {
  ```
- `public/enrol/lti/tests/local/ltiadvantage/task/sync_tool_grades_test.php:122`
  ```php
  protected function override_resource_completion_status_for_user(\stdClass $resource, int $userid,
  ```
- `public/enrol/lti/tests/local/ltiadvantage/task/sync_tool_grades_test.php:154`
  ```php
  public static function grade_sync_positive_cases(): array {
  ```
- `public/enrol/lti/tests/local/ltiadvantage/task/sync_tool_grades_test.php:170`
  ```php
  public function test_grade_sync_positive_case($statuscode): void {
  ```
- `public/enrol/lti/tests/local/ltiadvantage/task/sync_tool_grades_test.php:218`
  ```php
  public function test_grade_sync_chronological_syncs(): void {
  ```
- `public/enrol/lti/tests/local/ltiadvantage/task/sync_tool_grades_test.php:317`
  ```php
  public function test_grade_sync_multiple_resource_links(): void {
  ```
- `public/enrol/lti/tests/local/ltiadvantage/task/sync_tool_grades_test.php:390`
  ```php
  public function test_sync_grades_no_service_endpoint(): void {
  ```
- `public/enrol/lti/tests/local/ltiadvantage/task/sync_tool_grades_test.php:421`
  ```php
  public function test_sync_grades_disabled_instance(): void {
  ```
- `public/enrol/lti/tests/local/ltiadvantage/task/sync_tool_grades_test.php:451`
  ```php
  public function test_sync_grades_deleted_context(): void {
  ```
- `public/enrol/lti/tests/local/ltiadvantage/task/sync_tool_grades_test.php:488`
  ```php
  public function test_sync_grades_completion_required(): void {
  ```
- `public/enrol/lti/tests/local/ltiadvantage/task/sync_tool_grades_test.php:49`
  ```php
  protected function get_task_with_mocked_grade_service($statuscode = '200', $mockexception = false): sync_tool_grades {
  ```
- `public/enrol/lti/tests/local/ltiadvantage/task/sync_tool_grades_test.php:682`
  ```php
  public function test_sync_grades_failed_service_call(): void {
  ```
- `public/enrol/lti/tests/local/ltiadvantage/task/sync_tool_grades_test.php:744`
  ```php
  public function test_sync_grades_coupled_lineitem(): void {
  ```
- `public/enrol/lti/tests/local/ltiadvantage/task/sync_tool_grades_test.php:76`
  ```php
  protected function set_user_grade_for_resource(int $userid, float $grade, \stdClass $resource): float {
  ```
- `public/enrol/lti/tests/local/ltiadvantage/task/sync_tool_grades_test.php:818`
  ```php
  public function test_sync_grades_none_or_many_lineitems_activity_context(): void {
  ```
- `public/enrol/lti/tests/local/ltiadvantage/task/sync_tool_grades_test.php:895`
  ```php
  public function test_sync_grades_none_or_many_lineitems_course_context(): void {
  ```
- `public/enrol/lti/upgradelib.php:36`
  ```php
  function enrol_lti_verify_private_key() {
  ```
- `public/enrol/manual/db/upgrade.php:25`
  ```php
  function xmldb_enrol_manual_upgrade($oldversion) {
  ```
- `public/enrol/paypal/db/upgrade.php:43`
  ```php
  function xmldb_enrol_paypal_upgrade($oldversion) {
  ```
- `public/enrol/self/db/upgrade.php:25`
  ```php
  function xmldb_enrol_self_upgrade($oldversion) {
  ```
- `public/files/classes/task/asynchronous_mimetype_upgrade_task.php:35`
  ```php
  public function execute(): void {
  ```
- `public/files/tests/task/asynchronous_mimetype_upgrade_task_test.php:107`
  ```php
  public function test_execute(
  ```
- `public/files/tests/task/asynchronous_mimetype_upgrade_task_test.php:35`
  ```php
  public static function upgrade_mimetype_provider(): array {
  ```
- `public/filter/algebra/db/upgrade.php:31`
  ```php
  function xmldb_filter_algebra_upgrade($oldversion) {
  ```
- `public/filter/displayh5p/db/upgrade.php:31`
  ```php
  function xmldb_filter_displayh5p_upgrade($oldversion) {
  ```
- `public/filter/displayh5p/db/upgradelib.php:35`
  ```php
  function filter_displayh5p_reorder() {
  ```
- `public/filter/displayh5p/tests/upgradelib_test.php:38`
  ```php
  public function test_filter_displayh5p_reorder(): void {
  ```
- `public/filter/mathjaxloader/db/upgrade.php:29`
  ```php
  function xmldb_filter_mathjaxloader_upgrade($oldversion) {
  ```
- `public/filter/mediaplugin/db/upgrade.php:30`
  ```php
  function xmldb_filter_mediaplugin_upgrade($oldversion) {
  ```
- `public/filter/tex/db/upgrade.php:32`
  ```php
  function xmldb_filter_tex_upgrade($oldversion) {
  ```
- `public/grade/classes/component_gradeitem.php:115`
  ```php
  public function get_grade_itemid(): int {
  ```
- `public/grade/classes/component_gradeitem.php:157`
  ```php
  protected function get_scale(): ?stdClass {
  ```
- `public/grade/classes/component_gradeitem.php:178`
  ```php
  public function is_using_scale(): bool {
  ```
- `public/grade/classes/component_gradeitem.php:189`
  ```php
  public function is_using_direct_grading(): bool {
  ```
- `public/grade/classes/component_gradeitem.php:206`
  ```php
  public function is_using_advanced_grading(): bool {
  ```
- `public/grade/classes/component_gradeitem.php:215`
  ```php
  public function get_advanced_grading_method(): ?string {
  ```
- `public/grade/classes/component_gradeitem.php:230`
  ```php
  public function get_grading_component_name(): ?string {
  ```
- `public/grade/classes/component_gradeitem.php:247`
  ```php
  public function get_grading_component_subtype(): ?string {
  ```
- `public/grade/classes/component_gradeitem.php:268`
  ```php
  protected function allow_decimals(): bool {
  ```
- `public/grade/classes/component_gradeitem.php:277`
  ```php
  protected function get_grading_manager(): ?grading_manager {
  ```
- `public/grade/classes/component_gradeitem.php:288`
  ```php
  protected function get_advanced_grading_controller(): ?gradingform_controller {
  ```
- `public/grade/classes/component_gradeitem.php:307`
  ```php
  public function get_grade_menu(): array {
  ```
- `public/grade/classes/component_gradeitem.php:318`
  ```php
  public function check_grade_validity(?float $grade): bool {
  ```
- `public/grade/classes/component_gradeitem.php:369`
  ```php
  public function get_grade(int $gradeid): stdClass {
  ```
- `public/grade/classes/component_gradeitem.php:394`
  ```php
  public function get_formatted_grade_for_user(stdClass $gradeduser, stdClass $grader): ?stdClass {
  ```
- `public/grade/classes/component_gradeitem.php:459`
  ```php
  public function get_grade_item(): \grade_item {
  ```
- `public/grade/classes/component_gradeitem.php:490`
  ```php
  public function store_grade_from_formdata(stdClass $gradeduser, stdClass $grader, stdClass $formdata): bool {
  ```
- `public/grade/classes/component_gradeitem.php:522`
  ```php
  public function get_advanced_grading_instance(stdClass $grader, stdClass $grade, ?int $instanceid = null): ?gradingform_instance {
  ```
- `public/grade/classes/component_gradeitem.php:557`
  ```php
  public function send_student_notification(stdClass $gradeduser, stdClass $grader): void {
  ```
- `public/grade/classes/component_gradeitem.php:84`
  ```php
  public static function instance(string $component, context $context, string $itemname): self {
  ```
- `public/grade/classes/component_gradeitems.php:102`
  ```php
  public static function get_advancedgrading_itemnames_for_component(string $component): array {
  ```
- `public/grade/classes/component_gradeitems.php:118`
  ```php
  public static function is_advancedgrading_itemname(string $component, string $itemname): bool {
  ```
- `public/grade/classes/component_gradeitems.php:134`
  ```php
  public static function get_field_name_for_itemnumber(string $component, int $itemnumber, string $fieldname): string {
  ```
- `public/grade/classes/component_gradeitems.php:161`
  ```php
  public static function get_field_name_for_itemname(string $component, string $itemname, string $fieldname): string {
  ```
- `public/grade/classes/component_gradeitems.php:184`
  ```php
  public static function get_itemname_from_itemnumber(string $component, int $itemnumber): string {
  ```
- `public/grade/classes/component_gradeitems.php:212`
  ```php
  public static function get_itemnumber_from_itemname(string $component, string $itemname): int {
  ```
- `public/grade/classes/component_gradeitems.php:47`
  ```php
  protected static function get_component_classname(string $component): string {
  ```
- `public/grade/classes/component_gradeitems.php:57`
  ```php
  public static function get_itemname_mapping_for_component(string $component): array {
  ```
- `public/grade/classes/component_gradeitems.php:80`
  ```php
  public static function is_valid_itemname(string $component, string $itemname): bool {
  ```
- `public/grade/classes/component_gradeitems.php:92`
  ```php
  public static function defines_advancedgrading_itemnames_for_component(string $component): bool {
  ```
- `public/grade/classes/external/create_gradecategories.php:111`
  ```php
  public static function execute_returns(): external_single_structure {
  ```
- `public/grade/classes/external/create_gradecategories.php:127`
  ```php
  public static function create_gradecategories_from_data(int $courseid, array $categories): array {
  ```
- `public/grade/classes/external/create_gradecategories.php:46`
  ```php
  public static function execute_parameters(): external_function_parameters {
  ```
- `public/grade/classes/external/create_gradecategories.php:89`
  ```php
  public static function execute(int $courseid, array $categories): array {
  ```
- `public/grade/classes/external/get_enrolled_users_for_selector.php:139`
  ```php
  public static function execute_returns(): external_single_structure {
  ```
- `public/grade/classes/external/get_enrolled_users_for_selector.php:50`
  ```php
  public static function execute_parameters(): external_function_parameters {
  ```
- `public/grade/classes/external/get_enrolled_users_for_selector.php:70`
  ```php
  public static function execute(int $courseid, ?int $groupid = 0): array {
  ```
- `public/grade/classes/external/get_feedback.php:105`
  ```php
  public static function execute_returns(): external_single_structure {
  ```
- `public/grade/classes/external/get_feedback.php:44`
  ```php
  public static function execute_parameters(): external_function_parameters {
  ```
- `public/grade/classes/external/get_feedback.php:62`
  ```php
  public static function execute(int $courseid, int $userid, int $itemid): array {
  ```
- `public/grade/classes/external/get_gradable_users.php:123`
  ```php
  public static function execute_returns(): external_single_structure {
  ```
- `public/grade/classes/external/get_gradable_users.php:52`
  ```php
  public static function execute_parameters(): external_function_parameters {
  ```
- `public/grade/classes/external/get_gradable_users.php:74`
  ```php
  public static function execute(int $courseid, ?int $groupid = 0, bool $onlyactive = false): array {
  ```
- `public/grade/classes/external/get_grade_tree.php:42`
  ```php
  public static function execute_parameters(): external_function_parameters {
  ```
- `public/grade/classes/external/get_grade_tree.php:56`
  ```php
  public static function execute(int $courseid): string {
  ```
- `public/grade/classes/external/get_grade_tree.php:81`
  ```php
  public static function execute_returns(): external_value {
  ```
- `public/grade/classes/external/get_grade_tree.php:91`
  ```php
  private static function generate_course_grade_tree(\grade_category $gradecategory): array {
  ```
- `public/grade/classes/external/get_gradeitems.php:48`
  ```php
  public static function execute_parameters(): external_function_parameters {
  ```
- `public/grade/classes/external/get_gradeitems.php:64`
  ```php
  public static function execute(int $courseid): array {
  ```
- `public/grade/classes/external/get_gradeitems.php:94`
  ```php
  public static function execute_returns(): external_single_structure {
  ```
- `public/grade/classes/form/add_category.php:148`
  ```php
  protected function definition(): void {
  ```
- `public/grade/classes/form/add_category.php:330`
  ```php
  public function definition_after_data(): void {
  ```
- `public/grade/classes/form/add_category.php:552`
  ```php
  protected function get_context_for_dynamic_submission(): context {
  ```
- `public/grade/classes/form/add_category.php:563`
  ```php
  protected function check_access_for_dynamic_submission(): void {
  ```
- `public/grade/classes/form/add_category.php:573`
  ```php
  public function set_data_for_dynamic_submission(): void {
  ```
- `public/grade/classes/form/add_category.php:586`
  ```php
  protected function get_page_url_for_dynamic_submission(): moodle_url {
  ```
- `public/grade/classes/form/add_category.php:600`
  ```php
  public function process_dynamic_submission(): array {
  ```
- `public/grade/classes/form/add_category.php:60`
  ```php
  private function get_gradecategory(): array {
  ```
- `public/grade/classes/form/add_category.php:624`
  ```php
  public function validation($data, $files): array {
  ```
- `public/grade/classes/form/add_item.php:102`
  ```php
  protected function definition() {
  ```
- `public/grade/classes/form/add_item.php:365`
  ```php
  protected function get_context_for_dynamic_submission(): context {
  ```
- `public/grade/classes/form/add_item.php:376`
  ```php
  protected function check_access_for_dynamic_submission(): void {
  ```
- `public/grade/classes/form/add_item.php:386`
  ```php
  public function set_data_for_dynamic_submission(): void {
  ```
- `public/grade/classes/form/add_item.php:399`
  ```php
  protected function get_page_url_for_dynamic_submission(): moodle_url {
  ```
- `public/grade/classes/form/add_item.php:413`
  ```php
  public function process_dynamic_submission() {
  ```
- `public/grade/classes/form/add_item.php:517`
  ```php
  public function validation($data, $files): array {
  ```
- `public/grade/classes/form/add_item.php:52`
  ```php
  private function get_gradeitem(): array {
  ```
- `public/grade/classes/form/add_outcome.php:109`
  ```php
  protected function definition() {
  ```
- `public/grade/classes/form/add_outcome.php:304`
  ```php
  protected function get_context_for_dynamic_submission(): context {
  ```
- `public/grade/classes/form/add_outcome.php:315`
  ```php
  protected function check_access_for_dynamic_submission(): void {
  ```
- `public/grade/classes/form/add_outcome.php:325`
  ```php
  public function set_data_for_dynamic_submission(): void {
  ```
- `public/grade/classes/form/add_outcome.php:338`
  ```php
  protected function get_page_url_for_dynamic_submission(): moodle_url {
  ```
- `public/grade/classes/form/add_outcome.php:352`
  ```php
  public function process_dynamic_submission() {
  ```
- `public/grade/classes/form/add_outcome.php:483`
  ```php
  public function validation($data, $files): array {
  ```
- `public/grade/classes/form/add_outcome.php:52`
  ```php
  private function get_gradeitem(): array {
  ```
- `public/grade/classes/grades/grader/gradingpanel/point/external/fetch.php:156`
  ```php
  public static function get_fetch_data(
  ```
- `public/grade/classes/grades/grader/gradingpanel/point/external/fetch.php:192`
  ```php
  public static function execute_returns(): external_single_structure {
  ```
- `public/grade/classes/grades/grader/gradingpanel/point/external/fetch.php:56`
  ```php
  public static function execute_parameters(): external_function_parameters {
  ```
- `public/grade/classes/grades/grader/gradingpanel/point/external/fetch.php:96`
  ```php
  public static function execute(string $component, int $contextid, string $itemname, int $gradeduserid): array {
  ```
- `public/grade/classes/grades/grader/gradingpanel/point/external/store.php:107`
  ```php
  public static function execute(string $component, int $contextid, string $itemname, int $gradeduserid,
  ```
- `public/grade/classes/grades/grader/gradingpanel/point/external/store.php:183`
  ```php
  public static function execute_returns(): external_single_structure {
  ```
- `public/grade/classes/grades/grader/gradingpanel/point/external/store.php:54`
  ```php
  public static function execute_parameters(): external_function_parameters {
  ```
- `public/grade/classes/grades/grader/gradingpanel/scale/external/fetch.php:152`
  ```php
  public static function get_fetch_data(gradeitem $gradeitem, stdClass $gradeduser, int $maxgrade, ?string $gradername): array {
  ```
- `public/grade/classes/grades/grader/gradingpanel/scale/external/fetch.php:189`
  ```php
  public static function execute_returns(): external_single_structure {
  ```
- `public/grade/classes/grades/grader/gradingpanel/scale/external/fetch.php:57`
  ```php
  public static function execute_parameters(): external_function_parameters {
  ```
- `public/grade/classes/grades/grader/gradingpanel/scale/external/fetch.php:97`
  ```php
  public static function execute(string $component, int $contextid, string $itemname, int $gradeduserid): array {
  ```
- `public/grade/classes/grades/grader/gradingpanel/scale/external/store.php:104`
  ```php
  public static function execute(string $component, int $contextid, string $itemname, int $gradeduserid,
  ```
- `public/grade/classes/grades/grader/gradingpanel/scale/external/store.php:177`
  ```php
  public static function execute_returns(): external_single_structure {
  ```
- `public/grade/classes/grades/grader/gradingpanel/scale/external/store.php:54`
  ```php
  public static function execute_parameters(): external_function_parameters {
  ```
- `public/grade/classes/local/gradeitem/advancedgrading_mapping.php:42`
  ```php
  public static function get_advancedgrading_itemnames(): array;
  ```
- `public/grade/classes/local/gradeitem/fieldname_mapping.php:47`
  ```php
  public static function get_field_name_for_itemnumber(string $component, int $itemnumber, string $fieldname): string;
  ```
- `public/grade/classes/local/gradeitem/itemnumber_mapping.php:42`
  ```php
  public static function get_itemname_mapping_for_component(): array;
  ```
- `public/grade/classes/output/action_bar.php:39`
  ```php
  public function __construct(\context $context) {
  ```
- `public/grade/classes/output/course_outcomes_action_bar.php:35`
  ```php
  public function get_template(): string {
  ```
- `public/grade/classes/output/course_outcomes_action_bar.php:45`
  ```php
  public function export_for_template(\renderer_base $output): array {
  ```
- `public/grade/classes/output/export_action_bar.php:40`
  ```php
  public function __construct(\context $context, $unused, string $activeplugin) {
  ```
- `public/grade/classes/output/export_action_bar.php:53`
  ```php
  public function get_template(): string {
  ```
- `public/grade/classes/output/export_action_bar.php:63`
  ```php
  public function export_for_template(\renderer_base $output): array {
  ```
- `public/grade/classes/output/export_key_manager_action_bar.php:35`
  ```php
  public function get_template(): string {
  ```
- `public/grade/classes/output/export_key_manager_action_bar.php:45`
  ```php
  public function export_for_template(\renderer_base $output): array {
  ```
- `public/grade/classes/output/export_publish_action_bar.php:39`
  ```php
  public function __construct(\context $context, string $activeplugin) {
  ```
- `public/grade/classes/output/export_publish_action_bar.php:49`
  ```php
  public function get_template(): string {
  ```
- `public/grade/classes/output/export_publish_action_bar.php:59`
  ```php
  public function export_for_template(\renderer_base $output): array {
  ```
- `public/grade/classes/output/general_action_bar.php:55`
  ```php
  public function __construct(\context $context, moodle_url $activeurl, string $activetype, string $activeplugin) {
  ```
- `public/grade/classes/output/general_action_bar.php:68`
  ```php
  public function export_for_template(\renderer_base $output): array {
  ```
- `public/grade/classes/output/general_action_bar.php:85`
  ```php
  public function get_template(): string {
  ```
- `public/grade/classes/output/general_action_bar.php:94`
  ```php
  private function get_action_selector(): ?select_menu {
  ```
- `public/grade/classes/output/grade_letters_action_bar.php:35`
  ```php
  public function get_template(): string {
  ```
- `public/grade/classes/output/grade_letters_action_bar.php:45`
  ```php
  public function export_for_template(\renderer_base $output): array {
  ```
- `public/grade/classes/output/gradebook_setup_action_bar.php:36`
  ```php
  public function get_template(): string {
  ```
- `public/grade/classes/output/gradebook_setup_action_bar.php:46`
  ```php
  public function export_for_template(\renderer_base $output): array {
  ```
- `public/grade/classes/output/import_action_bar.php:40`
  ```php
  public function __construct(\context $context, $unused, string $activeplugin) {
  ```
- `public/grade/classes/output/import_action_bar.php:53`
  ```php
  public function get_template(): string {
  ```
- `public/grade/classes/output/import_action_bar.php:63`
  ```php
  public function export_for_template(\renderer_base $output): array {
  ```
- `public/grade/classes/output/import_key_manager_action_bar.php:35`
  ```php
  public function get_template(): string {
  ```
- `public/grade/classes/output/import_key_manager_action_bar.php:45`
  ```php
  public function export_for_template(\renderer_base $output): array {
  ```
- `public/grade/classes/output/manage_outcomes_action_bar.php:39`
  ```php
  public function __construct(\context $context, bool $hasoutcomes) {
  ```
- `public/grade/classes/output/manage_outcomes_action_bar.php:49`
  ```php
  public function get_template(): string {
  ```
- `public/grade/classes/output/manage_outcomes_action_bar.php:59`
  ```php
  public function export_for_template(\renderer_base $output): array {
  ```
- `public/grade/classes/output/penalty_indicator.php:41`
  ```php
  public function __construct(
  ```
- `public/grade/classes/output/penalty_indicator.php:64`
  ```php
  public function get_template(): string {
  ```
- `public/grade/classes/output/penalty_indicator.php:69`
  ```php
  public function export_for_template(renderer_base $output): array {
  ```
- `public/grade/classes/output/scales_action_bar.php:35`
  ```php
  public function get_template(): string {
  ```
- `public/grade/classes/output/scales_action_bar.php:45`
  ```php
  public function export_for_template(\renderer_base $output): array {
  ```
- `public/grade/classes/penalty_container.php:100`
  ```php
  public function get_grade_grade(): grade_grade {
  ```
- `public/grade/classes/penalty_container.php:112`
  ```php
  public function get_grade_before_penalties(): float {
  ```
- `public/grade/classes/penalty_container.php:123`
  ```php
  public function get_grade_after_penalties(): float {
  ```
- `public/grade/classes/penalty_container.php:137`
  ```php
  public function get_penalty(): float {
  ```
- `public/grade/classes/penalty_container.php:146`
  ```php
  public function get_min_grade(): int {
  ```
- `public/grade/classes/penalty_container.php:155`
  ```php
  public function get_max_grade(): float {
  ```
- `public/grade/classes/penalty_container.php:174`
  ```php
  public function aggregate_penalty(float $penalty): void {
  ```
- `public/grade/classes/penalty_container.php:190`
  ```php
  private static function clamp(float $value, float $min, float $max): float {
  ```
- `public/grade/classes/penalty_container.php:42`
  ```php
  public function __construct(
  ```
- `public/grade/classes/penalty_container.php:62`
  ```php
  public function get_userid(): int {
  ```
- `public/grade/classes/penalty_container.php:71`
  ```php
  public function get_submission_date(): int {
  ```
- `public/grade/classes/penalty_container.php:80`
  ```php
  public function get_due_date(): int {
  ```
- `public/grade/classes/penalty_container.php:90`
  ```php
  public function get_grade_item(): grade_item {
  ```
- `public/grade/classes/penalty_manager.php:108`
  ```php
  public static function is_penalty_enabled_for_module(string $module): bool {
  ```
- `public/grade/classes/penalty_manager.php:118`
  ```php
  private static function is_penalty_enabled_for_grade(grade_grade $grade): bool {
  ```
- `public/grade/classes/penalty_manager.php:154`
  ```php
  private static function calculate_penalties(penalty_container $container): penalty_container {
  ```
- `public/grade/classes/penalty_manager.php:181`
  ```php
  public static function apply_grade_penalty_to_user(
  ```
- `public/grade/classes/penalty_manager.php:207`
  ```php
  private static function apply_penalty(
  ```
- `public/grade/classes/penalty_manager.php:278`
  ```php
  public static function apply_grade_item_factors(
  ```
- `public/grade/classes/penalty_manager.php:295`
  ```php
  public static function show_penalty_indicator(grade_grade $grade): string {
  ```
- `public/grade/classes/penalty_manager.php:315`
  ```php
  public static function extend_navigation_course(navigation_node $navigation,
  ```
- `public/grade/classes/penalty_manager.php:344`
  ```php
  public static function extend_navigation_module(settings_navigation $settings, navigation_node $navref): void {
  ```
- `public/grade/classes/penalty_manager.php:374`
  ```php
  public static function recalculate_penalty(context $context, int $usermodified = 0): void {
  ```
- `public/grade/classes/penalty_manager.php:43`
  ```php
  public static function get_supported_modules(): array {
  ```
- `public/grade/classes/penalty_manager.php:60`
  ```php
  public static function get_enabled_modules(): array {
  ```
- `public/grade/classes/penalty_manager.php:69`
  ```php
  public static function enable_module(string $module): void {
  ```
- `public/grade/classes/penalty_manager.php:78`
  ```php
  public static function enable_modules(array $modules): void {
  ```
- `public/grade/classes/penalty_manager.php:88`
  ```php
  public static function disable_module(string $module): void {
  ```
- `public/grade/classes/penalty_manager.php:97`
  ```php
  public static function disable_modules(array $modules): void {
  ```
- `public/grade/classes/privacy/grade_grade_with_history.php:33`
  ```php
  public function __construct(?\stdClass $params = null, $fetch = true) {
  ```
- `public/grade/classes/privacy/provider.php:1080`
  ```php
  protected static function extract_grade_grade_from_record(stdClass $record, $ishistory = false) {
  ```
- `public/grade/classes/privacy/provider.php:1112`
  ```php
  protected static function extract_record($record, $prefix) {
  ```
- `public/grade/classes/privacy/provider.php:1131`
  ```php
  protected static function get_fields_sql($target, $alias, $prefix) {
  ```
- `public/grade/classes/privacy/provider.php:1164`
  ```php
  protected static function get_item_ids_from_course_ids($courseids) {
  ```
- `public/grade/classes/privacy/provider.php:1183`
  ```php
  protected static function recordset_loop_and_export(\moodle_recordset $recordset, $splitkey, $initial,
  ```
- `public/grade/classes/privacy/provider.php:1210`
  ```php
  protected static function transform_history_action($action) {
  ```
- `public/grade/classes/privacy/provider.php:1234`
  ```php
  protected static function transform_grade(grade_grade $gg, context $context, bool $ishistory) {
  ```
- `public/grade/classes/privacy/provider.php:1284`
  ```php
  protected static function delete_files(array $itemids, bool $ishistory, ?array $userids = null) {
  ```
- `public/grade/classes/privacy/provider.php:144`
  ```php
  public static function get_contexts_for_userid(int $userid): \core_privacy\local\request\contextlist {
  ```
- `public/grade/classes/privacy/provider.php:320`
  ```php
  public static function get_users_in_context(\core_privacy\local\request\userlist $userlist) {
  ```
- `public/grade/classes/privacy/provider.php:414`
  ```php
  public static function export_user_data(approved_contextlist $contextlist) {
  ```
- `public/grade/classes/privacy/provider.php:63`
  ```php
  public static function get_metadata(collection $collection): collection {
  ```
- `public/grade/classes/privacy/provider.php:739`
  ```php
  public static function delete_data_for_all_users_in_context(context $context) {
  ```
- `public/grade/classes/privacy/provider.php:771`
  ```php
  public static function delete_data_for_user(approved_contextlist $contextlist) {
  ```
- `public/grade/classes/privacy/provider.php:812`
  ```php
  public static function delete_data_for_users(\core_privacy\local\request\approved_userlist $userlist) {
  ```
- `public/grade/classes/privacy/provider.php:853`
  ```php
  protected static function delete_orphan_historical_grades($userid) {
  ```
- `public/grade/classes/privacy/provider.php:891`
  ```php
  protected static function export_user_data_outcomes_in_contexts(approved_contextlist $contextlist) {
  ```
- `public/grade/classes/privacy/provider.php:985`
  ```php
  protected static function export_user_data_scales_in_contexts(approved_contextlist $contextlist) {
  ```
- `public/grade/classes/table/gradepenalty_management_table.php:31`
  ```php
  protected function get_plugintype(): string {
  ```
- `public/grade/classes/table/gradepenalty_management_table.php:36`
  ```php
  protected function get_action_url(array $params = []): url {
  ```
- `public/grade/edit/letter/edit_form.php:33`
  ```php
  public function definition() {
  ```
- `public/grade/edit/outcome/edit_form.php:140`
  ```php
  function validation($data, $files) {
  ```
- `public/grade/edit/outcome/edit_form.php:32`
  ```php
  public function definition() {
  ```
- `public/grade/edit/outcome/edit_form.php:78`
  ```php
  function definition_after_data() {
  ```
- `public/grade/edit/outcome/export.php:92`
  ```php
  function format_csv($fields = array(), $delimiter = ';', $enclosure = '"') {
  ```
- `public/grade/edit/outcome/import_outcomes_form.php:33`
  ```php
  public function definition() {
  ```
- `public/grade/edit/outcome/index.php:252`
  ```php
  function grade_print_scale_link($courseid, $scale, $gpr) {
  ```
- `public/grade/edit/scale/edit_form.php:112`
  ```php
  function validation($data, $files) {
  ```
- `public/grade/edit/scale/edit_form.php:32`
  ```php
  function definition() {
  ```
- `public/grade/edit/scale/edit_form.php:73`
  ```php
  function definition_after_data() {
  ```
- `public/grade/edit/settings/form.php:37`
  ```php
  function definition() {
  ```
- `public/grade/edit/tree/calculation.php:161`
  ```php
  function get_grade_tree(&$gtree, $element, $current_itemid=null, $errors=null) {
  ```
- `public/grade/edit/tree/calculation_form.php:35`
  ```php
  function definition() {
  ```
- `public/grade/edit/tree/calculation_form.php:82`
  ```php
  function definition_after_data() {
  ```
- `public/grade/edit/tree/calculation_form.php:89`
  ```php
  function validation($data, $files) {
  ```
- `public/grade/edit/tree/category_form.php:272`
  ```php
  function definition_after_data() {
  ```
- `public/grade/edit/tree/category_form.php:34`
  ```php
  function definition() {
  ```
- `public/grade/edit/tree/category_form.php:509`
  ```php
  function validation($data, $files) {
  ```
- `public/grade/edit/tree/grade_form.php:137`
  ```php
  function definition_after_data() {
  ```
- `public/grade/edit/tree/grade_form.php:33`
  ```php
  function definition() {
  ```
- `public/grade/edit/tree/item_form.php:244`
  ```php
  function definition_after_data() {
  ```
- `public/grade/edit/tree/item_form.php:34`
  ```php
  function definition() {
  ```
- `public/grade/edit/tree/item_form.php:396`
  ```php
  function validation($data, $files) {
  ```
- `public/grade/edit/tree/lib.php:1055`
  ```php
  public function get_header_cell() {
  ```
- `public/grade/edit/tree/lib.php:1069`
  ```php
  public function get_category_cell($category, $levelclass, $params) {
  ```
- `public/grade/edit/tree/lib.php:1117`
  ```php
  public function get_item_cell($item, $params) {
  ```
- `public/grade/edit/tree/lib.php:1136`
  ```php
  public function __construct($params) {
  ```
- `public/grade/edit/tree/lib.php:1140`
  ```php
  public function get_header_cell() {
  ```
- `public/grade/edit/tree/lib.php:1146`
  ```php
  public function get_category_cell($category, $levelclass, $params) {
  ```
- `public/grade/edit/tree/lib.php:1157`
  ```php
  public function get_item_cell($item, $params) {
  ```
- `public/grade/edit/tree/lib.php:391`
  ```php
  static function get_weight_input($item) {
  ```
- `public/grade/edit/tree/lib.php:441`
  ```php
  static function format_number($number) {
  ```
- `public/grade/edit/tree/lib.php:455`
  ```php
  public static function element_deletable($element) {
  ```
- `public/grade/edit/tree/lib.php:483`
  ```php
  public static function element_duplicatable($element) {
  ```
- `public/grade/edit/tree/lib.php:503`
  ```php
  function move_elements($eids, $returnurl) {
  ```
- `public/grade/edit/tree/lib.php:542`
  ```php
  function get_deepest_level($element, $level=0, $deepest_level=1) {
  ```
- `public/grade/edit/tree/lib.php:54`
  ```php
  public function __construct($gtree, $moving, $gpr) {
  ```
- `public/grade/edit/tree/lib.php:578`
  ```php
  public static function update_gradecategory(grade_category $gradecategory, stdClass $data) {
  ```
- `public/grade/edit/tree/lib.php:744`
  ```php
  public static function factory($name, $params=array()) {
  ```
- `public/grade/edit/tree/lib.php:753`
  ```php
  public function get_category_cell($category, $levelclass, $params) {
  ```
- `public/grade/edit/tree/lib.php:759`
  ```php
  public function get_item_cell($item, $params) {
  ```
- `public/grade/edit/tree/lib.php:769`
  ```php
  public function __construct() {
  ```
- `public/grade/edit/tree/lib.php:802`
  ```php
  public function __construct($params) {
  ```
- `public/grade/edit/tree/lib.php:811`
  ```php
  public function get_header_cell() {
  ```
- `public/grade/edit/tree/lib.php:818`
  ```php
  public function get_category_cell($category, $levelclass, $params) {
  ```
- `public/grade/edit/tree/lib.php:863`
  ```php
  public function get_item_cell($item, $params) {
  ```
- `public/grade/edit/tree/lib.php:914`
  ```php
  protected function get_checkbox_togglegroup(grade_category $category): string {
  ```
- `public/grade/edit/tree/lib.php:937`
  ```php
  public function get_header_cell() {
  ```
- `public/grade/edit/tree/lib.php:944`
  ```php
  public function get_category_cell($category, $levelclass, $params) {
  ```
- `public/grade/edit/tree/lib.php:952`
  ```php
  public function get_item_cell($item, $params) {
  ```
- `public/grade/edit/tree/lib.php:980`
  ```php
  public function get_header_cell() {
  ```
- `public/grade/edit/tree/lib.php:986`
  ```php
  public function get_category_cell($category, $levelclass, $params) {
  ```
- `public/grade/edit/tree/lib.php:992`
  ```php
  public function get_item_cell($item, $params) {
  ```
- `public/grade/edit/tree/lib.php:99`
  ```php
  public function build_html_tree($element, $totals, $parents, $level, &$row_count) {
  ```
- `public/grade/edit/tree/outcomeitem_form.php:164`
  ```php
  function definition_after_data() {
  ```
- `public/grade/edit/tree/outcomeitem_form.php:246`
  ```php
  function validation($data, $files) {
  ```
- `public/grade/edit/tree/outcomeitem_form.php:32`
  ```php
  function definition() {
  ```
- `public/grade/export/grade_export_form.php:214`
  ```php
  public function get_data() {
  ```
- `public/grade/export/grade_export_form.php:25`
  ```php
  function definition() {
  ```
- `public/grade/export/key_form.php:35`
  ```php
  function definition() {
  ```
- `public/grade/export/lib.php:149`
  ```php
  function process_form($formdata) {
  ```
- `public/grade/export/lib.php:215`
  ```php
  public function track_exports() {
  ```
- `public/grade/export/lib.php:236`
  ```php
  public function format_grade($grade, $gradedisplayconst = null) {
  ```
- `public/grade/export/lib.php:268`
  ```php
  public function format_column_name($grade_item, $feedback=false, $gradedisplayname = null) {
  ```
- `public/grade/export/lib.php:289`
  ```php
  public function format_feedback($feedback, $grade = null) {
  ```
- `public/grade/export/lib.php:316`
  ```php
  public function display_preview($require_user_idnumber=false) {
  ```
- `public/grade/export/lib.php:411`
  ```php
  public function get_export_params() {
  ```
- `public/grade/export/lib.php:454`
  ```php
  public function print_continue() {
  ```
- `public/grade/export/lib.php:493`
  ```php
  public function get_export_url() {
  ```
- `public/grade/export/lib.php:510`
  ```php
  public static function convert_flat_displaytypes_to_array($displaytypes) {
  ```
- `public/grade/export/lib.php:564`
  ```php
  public static function convert_flat_itemids_to_array($itemids) {
  ```
- `public/grade/export/lib.php:587`
  ```php
  public function get_grade_publishing_url() {
  ```
- `public/grade/export/lib.php:609`
  ```php
  public static function export_bulk_export_data($id, $itemids, $exportfeedback, $onlyactive, $displaytype,
  ```
- `public/grade/export/lib.php:643`
  ```php
  public function __construct() {
  ```
- `public/grade/export/lib.php:648`
  ```php
  public function flush($buffersize) {
  ```
- `public/grade/export/lib.php:666`
  ```php
  public function track($grade_grade) {
  ```
- `public/grade/export/lib.php:697`
  ```php
  public function close() {
  ```
- `public/grade/export/lib.php:706`
  ```php
  function export_verify_grades($courseid) {
  ```
- `public/grade/export/lib.php:75`
  ```php
  public function __construct($course, $groupid, $formdata) {
  ```
- `public/grade/export/lib.php:97`
  ```php
  protected function deprecated_constructor($course,
  ```
- `public/grade/export/ods/classes/privacy/provider.php:43`
  ```php
  public static function get_reason(): string {
  ```
- `public/grade/export/ods/grade_export_ods.php:30`
  ```php
  public function __construct($course, $groupid, $formdata) {
  ```
- `public/grade/export/ods/grade_export_ods.php:40`
  ```php
  function print_grades() {
  ```
- `public/grade/export/ods/tests/event/events_test.php:31`
  ```php
  public function setUp(): void {
  ```
- `public/grade/export/ods/tests/event/events_test.php:39`
  ```php
  public function test_logging(): void {
  ```
- `public/grade/export/txt/classes/privacy/provider.php:43`
  ```php
  public static function get_reason(): string {
  ```
- `public/grade/export/txt/grade_export_txt.php:33`
  ```php
  public function __construct($course, $groupid, $formdata) {
  ```
- `public/grade/export/txt/grade_export_txt.php:41`
  ```php
  public function get_export_params() {
  ```
- `public/grade/export/txt/grade_export_txt.php:47`
  ```php
  public function print_grades() {
  ```
- `public/grade/export/txt/tests/event/events_test.php:31`
  ```php
  public function setUp(): void {
  ```
- `public/grade/export/txt/tests/event/events_test.php:39`
  ```php
  public function test_logging(): void {
  ```
- `public/grade/export/xls/classes/privacy/provider.php:43`
  ```php
  public static function get_reason(): string {
  ```
- `public/grade/export/xls/grade_export_xls.php:30`
  ```php
  public function __construct($course, $groupid, $formdata) {
  ```
- `public/grade/export/xls/grade_export_xls.php:40`
  ```php
  public function print_grades() {
  ```
- `public/grade/export/xls/tests/event/events_test.php:31`
  ```php
  public function setUp(): void {
  ```
- `public/grade/export/xls/tests/event/events_test.php:39`
  ```php
  public function test_logging(): void {
  ```
- `public/grade/export/xml/classes/privacy/provider.php:43`
  ```php
  public static function get_reason(): string {
  ```
- `public/grade/export/xml/grade_export_xml.php:32`
  ```php
  private static function xml_export_idnumber(string $idnumber): string {
  ```
- `public/grade/export/xml/grade_export_xml.php:42`
  ```php
  public function process_form($formdata) {
  ```
- `public/grade/export/xml/grade_export_xml.php:56`
  ```php
  public function print_grades($feedback = false) {
  ```
- `public/grade/export/xml/tests/event/events_test.php:31`
  ```php
  public function setUp(): void {
  ```
- `public/grade/export/xml/tests/event/events_test.php:39`
  ```php
  public function test_logging(): void {
  ```
- `public/grade/grading/classes/privacy/gradingform_legacy_polyfill.php:44`
  ```php
  public static function export_gradingform_instance_data(\context $context, int $instanceid, array $subcontext) {
  ```
- `public/grade/grading/classes/privacy/gradingform_legacy_polyfill.php:53`
  ```php
  public static function delete_gradingform_for_instances(array $instanceids) {
  ```
- `public/grade/grading/classes/privacy/gradingform_provider_v2.php:40`
  ```php
  public static function export_gradingform_instance_data(\context $context, int $instanceid, array $subcontext);
  ```
- `public/grade/grading/classes/privacy/gradingform_provider_v2.php:47`
  ```php
  public static function delete_gradingform_for_instances(array $instanceids);
  ```
- `public/grade/grading/classes/privacy/provider.php:117`
  ```php
  public static function get_users_in_context(\core_privacy\local\request\userlist $userlist) {
  ```
- `public/grade/grading/classes/privacy/provider.php:145`
  ```php
  public static function export_user_data(approved_contextlist $contextlist) {
  ```
- `public/grade/grading/classes/privacy/provider.php:173`
  ```php
  public static function export_item_data(\context $context, int $itemid, array $subcontext) {
  ```
- `public/grade/grading/classes/privacy/provider.php:203`
  ```php
  public static function delete_instance_data(\context $context, ?int $itemid = null) {
  ```
- `public/grade/grading/classes/privacy/provider.php:217`
  ```php
  public static function delete_data_for_instances(\context $context, array $itemids = []) {
  ```
- `public/grade/grading/classes/privacy/provider.php:255`
  ```php
  protected static function export_definitions(\context $context, array $subcontext, int $userid = 0) {
  ```
- `public/grade/grading/classes/privacy/provider.php:331`
  ```php
  protected static function export_grading_instances(\context $context, array $subcontext, int $definitionid, int $userid = 0) {
  ```
- `public/grade/grading/classes/privacy/provider.php:367`
  ```php
  public static function delete_data_for_all_users_in_context(\context $context) {
  ```
- `public/grade/grading/classes/privacy/provider.php:377`
  ```php
  public static function delete_data_for_user(approved_contextlist $contextlist) {
  ```
- `public/grade/grading/classes/privacy/provider.php:386`
  ```php
  public static function delete_data_for_users(\core_privacy\local\request\approved_userlist $userlist) {
  ```
- `public/grade/grading/classes/privacy/provider.php:54`
  ```php
  public static function get_metadata(collection $collection): collection {
  ```
- `public/grade/grading/classes/privacy/provider.php:91`
  ```php
  public static function get_contexts_for_userid(int $userid): contextlist {
  ```
- `public/grade/grading/form/guide/backup/moodle2/backup_gradingform_guide_plugin.class.php:40`
  ```php
  protected function define_definition_plugin_structure() {
  ```
- `public/grade/grading/form/guide/backup/moodle2/backup_gradingform_guide_plugin.class.php:89`
  ```php
  protected function define_instance_plugin_structure() {
  ```
- `public/grade/grading/form/guide/backup/moodle2/restore_gradingform_guide_plugin.class.php:112`
  ```php
  public function process_gradingform_guide_comment_legacy($data) {
  ```
- `public/grade/grading/form/guide/backup/moodle2/restore_gradingform_guide_plugin.class.php:126`
  ```php
  public function process_gradinform_guide_filling($data) {
  ```
- `public/grade/grading/form/guide/backup/moodle2/restore_gradingform_guide_plugin.class.php:41`
  ```php
  protected function define_definition_plugin_structure() {
  ```
- `public/grade/grading/form/guide/backup/moodle2/restore_gradingform_guide_plugin.class.php:64`
  ```php
  protected function define_instance_plugin_structure() {
  ```
- `public/grade/grading/form/guide/backup/moodle2/restore_gradingform_guide_plugin.class.php:82`
  ```php
  public function process_gradingform_guide_criterion($data) {
  ```
- `public/grade/grading/form/guide/backup/moodle2/restore_gradingform_guide_plugin.class.php:98`
  ```php
  public function process_gradingform_guide_comment($data) {
  ```
- `public/grade/grading/form/guide/classes/grades/grader/gradingpanel/external/fetch.php:101`
  ```php
  public static function execute(string $component, int $contextid, string $itemname, int $gradeduserid): array {
  ```
- `public/grade/grading/form/guide/classes/grades/grader/gradingpanel/external/fetch.php:152`
  ```php
  public static function get_fetch_data(gradeitem $gradeitem, stdClass $gradeduser): array {
  ```
- `public/grade/grading/form/guide/classes/grades/grader/gradingpanel/external/fetch.php:255`
  ```php
  public static function execute_returns(): external_single_structure {
  ```
- `public/grade/grading/form/guide/classes/grades/grader/gradingpanel/external/fetch.php:302`
  ```php
  protected static function get_formatted_text(context $context, int $definitionid, string $filearea, string $text, int $format): string {
  ```
- `public/grade/grading/form/guide/classes/grades/grader/gradingpanel/external/fetch.php:61`
  ```php
  public static function execute_parameters(): external_function_parameters {
  ```
- `public/grade/grading/form/guide/classes/grades/grader/gradingpanel/external/store.php:112`
  ```php
  public static function execute(string $component, int $contextid, string $itemname, int $gradeduserid,
  ```
- `public/grade/grading/form/guide/classes/grades/grader/gradingpanel/external/store.php:183`
  ```php
  public static function execute_returns(): external_single_structure {
  ```
- `public/grade/grading/form/guide/classes/grades/grader/gradingpanel/external/store.php:58`
  ```php
  public static function execute_parameters(): external_function_parameters {
  ```
- `public/grade/grading/form/guide/classes/privacy/provider.php:106`
  ```php
  public static function export_user_preferences(int $userid) {
  ```
- `public/grade/grading/form/guide/classes/privacy/provider.php:50`
  ```php
  public static function get_metadata(collection $collection): collection {
  ```
- `public/grade/grading/form/guide/classes/privacy/provider.php:76`
  ```php
  public static function export_gradingform_instance_data(\context $context, int $instanceid, array $subcontext) {
  ```
- `public/grade/grading/form/guide/classes/privacy/provider.php:96`
  ```php
  public static function delete_gradingform_for_instances(array $instanceids) {
  ```
- `public/grade/grading/form/guide/db/upgrade.php:35`
  ```php
  function xmldb_gradingform_guide_upgrade($oldversion) {
  ```
- `public/grade/grading/form/guide/edit_form.php:121`
  ```php
  public function validation($data, $files) {
  ```
- `public/grade/grading/form/guide/edit_form.php:148`
  ```php
  public function get_data() {
  ```
- `public/grade/grading/form/guide/edit_form.php:165`
  ```php
  public function need_confirm_regrading($controller) {
  ```
- `public/grade/grading/form/guide/edit_form.php:44`
  ```php
  public function definition() {
  ```
- `public/grade/grading/form/guide/edit_form.php:98`
  ```php
  public function definition_after_data() {
  ```
- `public/grade/grading/form/guide/guideeditor.php:148`
  ```php
  protected function prepare_data($value = null, $withvalidation = false) {
  ```
- `public/grade/grading/form/guide/guideeditor.php:312`
  ```php
  protected function get_next_id($ids) {
  ```
- `public/grade/grading/form/guide/guideeditor.php:331`
  ```php
  public function non_js_button_pressed($value) {
  ```
- `public/grade/grading/form/guide/guideeditor.php:345`
  ```php
  public function validate($value) {
  ```
- `public/grade/grading/form/guide/guideeditor.php:360`
  ```php
  public function exportValue(&$submitvalues, $assoc = false) {
  ```
- `public/grade/grading/form/guide/guideeditor.php:56`
  ```php
  public function __construct($elementname=null, $elementlabel=null, $attributes=null) {
  ```
- `public/grade/grading/form/guide/guideeditor.php:65`
  ```php
  public function getHelpButton() {
  ```
- `public/grade/grading/form/guide/guideeditor.php:74`
  ```php
  public function getElementTemplateType() {
  ```
- `public/grade/grading/form/guide/guideeditor.php:85`
  ```php
  public function add_regrade_confirmation($changelevel) {
  ```
- `public/grade/grading/form/guide/guideeditor.php:94`
  ```php
  public function toHtml() {
  ```
- `public/grade/grading/form/guide/lib.php:1020`
  ```php
  function gradingform_guide_get_fontawesome_icon_map(): array {
  ```
- `public/grade/grading/form/guide/lib.php:111`
  ```php
  public function update_definition(stdClass $newdefinition, $usermodified = null) {
  ```
- `public/grade/grading/form/guide/lib.php:134`
  ```php
  public function update_or_check_guide(stdClass $newdefinition, $usermodified = null, $doupdate = false) {
  ```
- `public/grade/grading/form/guide/lib.php:290`
  ```php
  public function mark_for_regrade() {
  ```
- `public/grade/grading/form/guide/lib.php:304`
  ```php
  protected function load_definition() {
  ```
- `public/grade/grading/form/guide/lib.php:373`
  ```php
  public static function get_default_options() {
  ```
- `public/grade/grading/form/guide/lib.php:386`
  ```php
  public function get_options() {
  ```
- `public/grade/grading/form/guide/lib.php:403`
  ```php
  public function get_definition_for_editing($addemptycriterion = false) {
  ```
- `public/grade/grading/form/guide/lib.php:440`
  ```php
  public function get_definition_copy(gradingform_controller $target) {
  ```
- `public/grade/grading/form/guide/lib.php:467`
  ```php
  public static function description_form_field_options($context) {
  ```
- `public/grade/grading/form/guide/lib.php:481`
  ```php
  public function get_formatted_description() {
  ```
- `public/grade/grading/form/guide/lib.php:506`
  ```php
  public function get_renderer(moodle_page $page) {
  ```
- `public/grade/grading/form/guide/lib.php:516`
  ```php
  public function render_preview(moodle_page $page) {
  ```
- `public/grade/grading/form/guide/lib.php:547`
  ```php
  protected function delete_plugin_definition() {
  ```
- `public/grade/grading/form/guide/lib.php:576`
  ```php
  public function get_or_create_instance($instanceid, $raterid, $itemid) {
  ```
- `public/grade/grading/form/guide/lib.php:608`
  ```php
  public function render_grade($page, $itemid, $gradinginfo, $defaultcontent, $cangrade) {
  ```
- `public/grade/grading/form/guide/lib.php:620`
  ```php
  public static function sql_search_from_tables($gdid) {
  ```
- `public/grade/grading/form/guide/lib.php:636`
  ```php
  public static function sql_search_where($token) {
  ```
- `public/grade/grading/form/guide/lib.php:654`
  ```php
  public function get_min_max_score() {
  ```
- `public/grade/grading/form/guide/lib.php:681`
  ```php
  public static function get_external_definition_details() {
  ```
- `public/grade/grading/form/guide/lib.php:717`
  ```php
  public static function get_external_instance_filling_details() {
  ```
- `public/grade/grading/form/guide/lib.php:754`
  ```php
  public function cancel() {
  ```
- `public/grade/grading/form/guide/lib.php:768`
  ```php
  public function copy($raterid, $itemid) {
  ```
- `public/grade/grading/form/guide/lib.php:76`
  ```php
  public function extend_settings_navigation(settings_navigation $settingsnav, ?navigation_node $node=null) {
  ```
- `public/grade/grading/form/guide/lib.php:786`
  ```php
  public function is_empty_form($elementvalue) {
  ```
- `public/grade/grading/form/guide/lib.php:806`
  ```php
  public function validate_grading_element($elementvalue) {
  ```
- `public/grade/grading/form/guide/lib.php:834`
  ```php
  public function get_guide_filling($force = false) {
  ```
- `public/grade/grading/form/guide/lib.php:854`
  ```php
  public function update($data) {
  ```
- `public/grade/grading/form/guide/lib.php:894`
  ```php
  public function clear_attempt($data) {
  ```
- `public/grade/grading/form/guide/lib.php:908`
  ```php
  public function get_grade() {
  ```
- `public/grade/grading/form/guide/lib.php:92`
  ```php
  public function extend_navigation(global_navigation $navigation, ?navigation_node $node=null) {
  ```
- `public/grade/grading/form/guide/lib.php:942`
  ```php
  public function render_grading_element($page, $gradingformelement) {
  ```
- `public/grade/grading/form/guide/renderer.php:365`
  ```php
  public function comment_template($mode, $elementname = '{NAME}', $comment = null) {
  ```
- `public/grade/grading/form/guide/renderer.php:461`
  ```php
  protected function guide_template($mode, $options, $elementname, $criteriastr, $commentstr) {
  ```
- `public/grade/grading/form/guide/renderer.php:550`
  ```php
  protected function guide_edit_options($mode, $options) {
  ```
- `public/grade/grading/form/guide/renderer.php:61`
  ```php
  public function criterion_template($mode, $options, $elementname = '{NAME}', $criterion = null, $value = null,
  ```
- `public/grade/grading/form/guide/renderer.php:626`
  ```php
  public function display_guide($criteria, $comments, $options, $mode, $elementname = null, $values = null,
  ```
- `public/grade/grading/form/guide/renderer.php:706`
  ```php
  protected function get_css_class_suffix($idx, $maxidx) {
  ```
- `public/grade/grading/form/guide/renderer.php:730`
  ```php
  public function display_instances($instances, $defaultcontent, $cangrade) {
  ```
- `public/grade/grading/form/guide/renderer.php:750`
  ```php
  public function display_instance(gradingform_guide_instance $instance, $idx, $cangrade) {
  ```
- `public/grade/grading/form/guide/renderer.php:774`
  ```php
  public function display_regrade_confirmation($elementname, $changelevel, $value) {
  ```
- `public/grade/grading/form/guide/renderer.php:796`
  ```php
  public function display_guide_mapping_explained($scores) {
  ```
- `public/grade/grading/form/guide/tests/behat/behat_gradingform_guide.php:113`
  ```php
  public function i_edit_the_marking_guide_criterion_with_the_following_values(string $criterionname, TableNode $fields) {
  ```
- `public/grade/grading/form/guide/tests/behat/behat_gradingform_guide.php:163`
  ```php
  public function i_define_the_following_frequently_used_comments(TableNode $commentstable) {
  ```
- `public/grade/grading/form/guide/tests/behat/behat_gradingform_guide.php:204`
  ```php
  public function i_grade_by_filling_the_marking_guide_with(TableNode $guide) {
  ```
- `public/grade/grading/form/guide/tests/behat/behat_gradingform_guide.php:253`
  ```php
  protected function set_guide_field_value($name, $value, $visible = false) {
  ```
- `public/grade/grading/form/guide/tests/behat/behat_gradingform_guide.php:59`
  ```php
  public function i_define_the_following_marking_guide(TableNode $guide) {
  ```
- `public/grade/grading/form/guide/tests/generator/criterion.php:105`
  ```php
  public function get_all_values(int $sortorder): array {
  ```
- `public/grade/grading/form/guide/tests/generator/criterion.php:56`
  ```php
  public function __construct(string $shortname, string $description, string $descriptionmarkers, float $maxscore) {
  ```
- `public/grade/grading/form/guide/tests/generator/criterion.php:68`
  ```php
  public function get_description(): string {
  ```
- `public/grade/grading/form/guide/tests/generator/criterion.php:77`
  ```php
  public function get_descriptionmarkers(): string {
  ```
- `public/grade/grading/form/guide/tests/generator/criterion.php:86`
  ```php
  public function get_shortname(): string {
  ```
- `public/grade/grading/form/guide/tests/generator/criterion.php:95`
  ```php
  public function get_maxscore(): float {
  ```
- `public/grade/grading/form/guide/tests/generator/guide.php:106`
  ```php
  public function add_criteria(criterion $criterion): self {
  ```
- `public/grade/grading/form/guide/tests/generator/guide.php:117`
  ```php
  protected function get_critiera_as_array(): array {
  ```
- `public/grade/grading/form/guide/tests/generator/guide.php:59`
  ```php
  public function __construct(string $name, string $description) {
  ```
- `public/grade/grading/form/guide/tests/generator/guide.php:70`
  ```php
  public function get_definition(): stdClass {
  ```
- `public/grade/grading/form/guide/tests/generator/guide.php:95`
  ```php
  public function set_option(string $key, $value): self {
  ```
- `public/grade/grading/form/guide/tests/generator/lib.php:100`
  ```php
  protected function get_guide(string $name, string $description): guide {
  ```
- `public/grade/grading/form/guide/tests/generator/lib.php:115`
  ```php
  protected function get_criterion(
  ```
- `public/grade/grading/form/guide/tests/generator/lib.php:131`
  ```php
  public function get_criterion_for_values(gradingform_controller $controller, string $shortname): ?stdClass {
  ```
- `public/grade/grading/form/guide/tests/generator/lib.php:154`
  ```php
  public function get_submitted_form_data(gradingform_guide_controller $controller, int $itemid, array $values): array {
  ```
- `public/grade/grading/form/guide/tests/generator/lib.php:176`
  ```php
  public function get_test_guide(
  ```
- `public/grade/grading/form/guide/tests/generator/lib.php:218`
  ```php
  public function get_test_form_data(
  ```
- `public/grade/grading/form/guide/tests/generator/lib.php:55`
  ```php
  public function create_instance(
  ```
- `public/grade/grading/form/guide/tests/generator_test.php:117`
  ```php
  public function test_get_criterion_for_values(): void {
  ```
- `public/grade/grading/form/guide/tests/generator_test.php:165`
  ```php
  public function test_get_test_guide(): void {
  ```
- `public/grade/grading/form/guide/tests/generator_test.php:194`
  ```php
  public function test_get_submitted_form_data(): void {
  ```
- `public/grade/grading/form/guide/tests/generator_test.php:242`
  ```php
  public function test_get_test_form_data(): void {
  ```
- `public/grade/grading/form/guide/tests/generator_test.php:45`
  ```php
  public function test_guide_creation(): void {
  ```
- `public/grade/grading/form/guide/tests/grades/grader/gradingpanel/external/fetch_test.php:164`
  ```php
  public function test_execute_fetch_graded(): void {
  ```
- `public/grade/grading/form/guide/tests/grades/grader/gradingpanel/external/fetch_test.php:181`
  ```php
  public function test_execute_fetch_does_not_return_data_to_other_students(): void {
  ```
- `public/grade/grading/form/guide/tests/grades/grader/gradingpanel/external/fetch_test.php:202`
  ```php
  public function test_execute_fetch_return_data_to_graded_user(): void {
  ```
- `public/grade/grading/form/guide/tests/grades/grader/gradingpanel/external/fetch_test.php:219`
  ```php
  private function execute_and_assert_fetch($forum, $controller, $definition, $fetcheruser, $grader, $gradeduser) {
  ```
- `public/grade/grading/form/guide/tests/grades/grader/gradingpanel/external/fetch_test.php:308`
  ```php
  protected function get_forum_instance(array $config = []): forum_entity {
  ```
- `public/grade/grading/form/guide/tests/grades/grader/gradingpanel/external/fetch_test.php:326`
  ```php
  protected function get_test_data(): array {
  ```
- `public/grade/grading/form/guide/tests/grades/grader/gradingpanel/external/fetch_test.php:378`
  ```php
  protected function get_test_form_data(
  ```
- `public/grade/grading/form/guide/tests/grades/grader/gradingpanel/external/fetch_test.php:40`
  ```php
  public function test_execute_invalid_component(): void {
  ```
- `public/grade/grading/form/guide/tests/grades/grader/gradingpanel/external/fetch_test.php:53`
  ```php
  public function test_execute_invalid_itemname(): void {
  ```
- `public/grade/grading/form/guide/tests/grades/grader/gradingpanel/external/fetch_test.php:66`
  ```php
  public function test_execute_incorrect_type(): void {
  ```
- `public/grade/grading/form/guide/tests/grades/grader/gradingpanel/external/fetch_test.php:88`
  ```php
  public function test_execute_fetch_empty(): void {
  ```
- `public/grade/grading/form/guide/tests/grades/grader/gradingpanel/external/store_test.php:106`
  ```php
  public function test_execute_store_graded(): void {
  ```
- `public/grade/grading/form/guide/tests/grades/grader/gradingpanel/external/store_test.php:201`
  ```php
  protected function get_forum_instance(array $config = []): forum_entity {
  ```
- `public/grade/grading/form/guide/tests/grades/grader/gradingpanel/external/store_test.php:219`
  ```php
  protected function get_test_data(): array {
  ```
- `public/grade/grading/form/guide/tests/grades/grader/gradingpanel/external/store_test.php:40`
  ```php
  public function test_execute_invalid_component(): void {
  ```
- `public/grade/grading/form/guide/tests/grades/grader/gradingpanel/external/store_test.php:53`
  ```php
  public function test_execute_invalid_itemname(): void {
  ```
- `public/grade/grading/form/guide/tests/grades/grader/gradingpanel/external/store_test.php:66`
  ```php
  public function test_execute_incorrect_type(): void {
  ```
- `public/grade/grading/form/guide/tests/grades/grader/gradingpanel/external/store_test.php:87`
  ```php
  public function test_execute_disabled(): void {
  ```
- `public/grade/grading/form/guide/tests/guide_test.php:39`
  ```php
  public function test_get_or_create_instance(): void {
  ```
- `public/grade/grading/form/guide/tests/privacy/provider_test.php:124`
  ```php
  public function test_delete_gradingform_for_instances(): void {
  ```
- `public/grade/grading/form/guide/tests/privacy/provider_test.php:179`
  ```php
  protected function get_test_guide(\context_module $context): \gradingform_guide_controller {
  ```
- `public/grade/grading/form/guide/tests/privacy/provider_test.php:197`
  ```php
  protected function get_test_form_data(
  ```
- `public/grade/grading/form/guide/tests/privacy/provider_test.php:47`
  ```php
  public function test_export_user_preferences_not_defined(): void {
  ```
- `public/grade/grading/form/guide/tests/privacy/provider_test.php:58`
  ```php
  public function test_export_user_preferences(): void {
  ```
- `public/grade/grading/form/guide/tests/privacy/provider_test.php:86`
  ```php
  public function test_get_gradingform_export_data(): void {
  ```
- `public/grade/grading/form/lib.php:1011`
  ```php
  public function submit_and_get_grade($elementvalue, $itemid) {
  ```
- `public/grade/grading/form/lib.php:1067`
  ```php
  public function validate_grading_element($elementvalue) {
  ```
- `public/grade/grading/form/lib.php:107`
  ```php
  public function get_context() {
  ```
- `public/grade/grading/form/lib.php:1082`
  ```php
  public function default_validation_error_message() {
  ```
- `public/grade/grading/form/lib.php:116`
  ```php
  public function get_component() {
  ```
- `public/grade/grading/form/lib.php:125`
  ```php
  public function get_area() {
  ```
- `public/grade/grading/form/lib.php:134`
  ```php
  public function get_areaid() {
  ```
- `public/grade/grading/form/lib.php:146`
  ```php
  public function is_form_defined() {
  ```
- `public/grade/grading/form/lib.php:155`
  ```php
  public function is_form_available() {
  ```
- `public/grade/grading/form/lib.php:164`
  ```php
  public function is_shared_template() {
  ```
- `public/grade/grading/form/lib.php:177`
  ```php
  public function is_own_form($userid = null) {
  ```
- `public/grade/grading/form/lib.php:195`
  ```php
  public function form_unavailable_notification() {
  ```
- `public/grade/grading/form/lib.php:208`
  ```php
  public function get_editor_url(?moodle_url $returnurl = null) {
  ```
- `public/grade/grading/form/lib.php:229`
  ```php
  public function extend_settings_navigation(settings_navigation $settingsnav, ?navigation_node $node=null) {
  ```
- `public/grade/grading/form/lib.php:242`
  ```php
  public function extend_navigation(global_navigation $navigation, ?navigation_node $node=null) {
  ```
- `public/grade/grading/form/lib.php:252`
  ```php
  public function get_definition($force = false) {
  ```
- `public/grade/grading/form/lib.php:265`
  ```php
  public function get_definition_copy(gradingform_controller $target) {
  ```
- `public/grade/grading/form/lib.php:300`
  ```php
  public function update_definition(stdClass $definition, $usermodified = null) {
  ```
- `public/grade/grading/form/lib.php:373`
  ```php
  public function get_formatted_description() {
  ```
- `public/grade/grading/form/lib.php:389`
  ```php
  public function get_current_instance($raterid, $itemid, $idonly = false) {
  ```
- `public/grade/grading/form/lib.php:421`
  ```php
  public function get_active_instances($itemid) {
  ```
- `public/grade/grading/form/lib.php:442`
  ```php
  public function get_all_active_instances($since = 0) {
  ```
- `public/grade/grading/form/lib.php:462`
  ```php
  public function has_active_instances() {
  ```
- `public/grade/grading/form/lib.php:481`
  ```php
  protected function get_instance($instance) {
  ```
- `public/grade/grading/form/lib.php:504`
  ```php
  public function create_instance($raterid, $itemid = null) {
  ```
- `public/grade/grading/form/lib.php:526`
  ```php
  public function get_or_create_instance($instanceid, $raterid, $itemid) {
  ```
- `public/grade/grading/form/lib.php:548`
  ```php
  public function fetch_instance(int $raterid, int $itemid, ?int $instanceid): gradingform_instance {
  ```
- `public/grade/grading/form/lib.php:591`
  ```php
  public function delete_definition() {
  ```
- `public/grade/grading/form/lib.php:615`
  ```php
  public static function sql_search_from_tables($gdid) {
  ```
- `public/grade/grading/form/lib.php:629`
  ```php
  public static function sql_search_where($token) {
  ```
- `public/grade/grading/form/lib.php:646`
  ```php
  protected function load_definition() {
  ```
- `public/grade/grading/form/lib.php:666`
  ```php
  protected function get_method_name() {
  ```
- `public/grade/grading/form/lib.php:684`
  ```php
  public function render_grade($page, $itemid, $gradinginfo, $defaultcontent, $cangrade) {
  ```
- `public/grade/grading/form/lib.php:742`
  ```php
  public static function get_external_definition_details() {
  ```
- `public/grade/grading/form/lib.php:760`
  ```php
  public static function get_external_instance_filling_details() {
  ```
- `public/grade/grading/form/lib.php:813`
  ```php
  public function __construct($controller, $data) {
  ```
- `public/grade/grading/form/lib.php:826`
  ```php
  public static function create_new($definitionid, $raterid, $itemid) {
  ```
- `public/grade/grading/form/lib.php:848`
  ```php
  public function copy($raterid, $itemid) {
  ```
- `public/grade/grading/form/lib.php:866`
  ```php
  public function get_current_instance() {
  ```
- `public/grade/grading/form/lib.php:878`
  ```php
  public function get_controller() {
  ```
- `public/grade/grading/form/lib.php:888`
  ```php
  public function get_data($key) {
  ```
- `public/grade/grading/form/lib.php:900`
  ```php
  public function get_id() {
  ```
- `public/grade/grading/form/lib.php:909`
  ```php
  public function get_status() {
  ```
- `public/grade/grading/form/lib.php:90`
  ```php
  public function __construct(stdClass $context, $component, $area, $areaid) {
  ```
- `public/grade/grading/form/lib.php:916`
  ```php
  protected function make_active() {
  ```
- `public/grade/grading/form/lib.php:940`
  ```php
  public function cancel() {
  ```
- `public/grade/grading/form/lib.php:953`
  ```php
  public function update($elementvalue) {
  ```
- `public/grade/grading/form/lib.php:985`
  ```php
  public function is_empty_form($elementvalue) {
  ```
- `public/grade/grading/form/lib.php:995`
  ```php
  public function clear_attempt($data) {
  ```
- `public/grade/grading/form/rubric/backup/moodle2/backup_gradingform_rubric_plugin.class.php:39`
  ```php
  protected function define_definition_plugin_structure() {
  ```
- `public/grade/grading/form/rubric/backup/moodle2/backup_gradingform_rubric_plugin.class.php:86`
  ```php
  protected function define_instance_plugin_structure() {
  ```
- `public/grade/grading/form/rubric/backup/moodle2/restore_gradingform_rubric_plugin.class.php:112`
  ```php
  public function process_gradinform_rubric_filling($data) {
  ```
- `public/grade/grading/form/rubric/backup/moodle2/restore_gradingform_rubric_plugin.class.php:41`
  ```php
  protected function define_definition_plugin_structure() {
  ```
- `public/grade/grading/form/rubric/backup/moodle2/restore_gradingform_rubric_plugin.class.php:59`
  ```php
  protected function define_instance_plugin_structure() {
  ```
- `public/grade/grading/form/rubric/backup/moodle2/restore_gradingform_rubric_plugin.class.php:77`
  ```php
  public function process_gradingform_rubric_criterion($data) {
  ```
- `public/grade/grading/form/rubric/backup/moodle2/restore_gradingform_rubric_plugin.class.php:96`
  ```php
  public function process_gradingform_rubric_level($data) {
  ```
- `public/grade/grading/form/rubric/classes/grades/grader/gradingpanel/external/fetch.php:146`
  ```php
  public static function get_fetch_data(gradeitem $gradeitem, stdClass $gradeduser): array {
  ```
- `public/grade/grading/form/rubric/classes/grades/grader/gradingpanel/external/fetch.php:271`
  ```php
  public static function execute_returns(): external_single_structure {
  ```
- `public/grade/grading/form/rubric/classes/grades/grader/gradingpanel/external/fetch.php:313`
  ```php
  protected static function get_formatted_text(context $context, int $definitionid, string $filearea, string $text, int $format): string {
  ```
- `public/grade/grading/form/rubric/classes/grades/grader/gradingpanel/external/fetch.php:60`
  ```php
  public static function execute_parameters(): external_function_parameters {
  ```
- `public/grade/grading/form/rubric/classes/grades/grader/gradingpanel/external/fetch.php:95`
  ```php
  public static function execute(string $component, int $contextid, string $itemname, int $gradeduserid): array {
  ```
- `public/grade/grading/form/rubric/classes/grades/grader/gradingpanel/external/store.php:107`
  ```php
  public static function execute(string $component, int $contextid, string $itemname, int $gradeduserid, bool $notifyuser,
  ```
- `public/grade/grading/form/rubric/classes/grades/grader/gradingpanel/external/store.php:181`
  ```php
  public static function execute_returns(): external_single_structure {
  ```
- `public/grade/grading/form/rubric/classes/grades/grader/gradingpanel/external/store.php:57`
  ```php
  public static function execute_parameters(): external_function_parameters {
  ```
- `public/grade/grading/form/rubric/classes/privacy/provider.php:47`
  ```php
  public static function get_metadata(collection $collection): collection {
  ```
- `public/grade/grading/form/rubric/classes/privacy/provider.php:64`
  ```php
  public static function export_gradingform_instance_data(\context $context, int $instanceid, array $subcontext) {
  ```
- `public/grade/grading/form/rubric/classes/privacy/provider.php:85`
  ```php
  public static function delete_gradingform_for_instances(array $instanceids) {
  ```
- `public/grade/grading/form/rubric/db/upgrade.php:31`
  ```php
  function xmldb_gradingform_rubric_upgrade($oldversion) {
  ```
- `public/grade/grading/form/rubric/edit_form.php:116`
  ```php
  public function validation($data, $files) {
  ```
- `public/grade/grading/form/rubric/edit_form.php:142`
  ```php
  public function get_data() {
  ```
- `public/grade/grading/form/rubric/edit_form.php:159`
  ```php
  public function need_confirm_regrading($controller) {
  ```
- `public/grade/grading/form/rubric/edit_form.php:43`
  ```php
  public function definition() {
  ```
- `public/grade/grading/form/rubric/edit_form.php:93`
  ```php
  public function definition_after_data() {
  ```
- `public/grade/grading/form/rubric/lib.php:108`
  ```php
  public function update_definition(stdClass $newdefinition, $usermodified = null) {
  ```
- `public/grade/grading/form/rubric/lib.php:130`
  ```php
  public function update_or_check_rubric(stdClass $newdefinition, $usermodified = null, $doupdate = false) {
  ```
- `public/grade/grading/form/rubric/lib.php:298`
  ```php
  public function mark_for_regrade() {
  ```
- `public/grade/grading/form/rubric/lib.php:312`
  ```php
  protected function load_definition() {
  ```
- `public/grade/grading/form/rubric/lib.php:368`
  ```php
  public static function get_default_options() {
  ```
- `public/grade/grading/form/rubric/lib.php:391`
  ```php
  public function get_options() {
  ```
- `public/grade/grading/form/rubric/lib.php:413`
  ```php
  public function get_definition_for_editing($addemptycriterion = false) {
  ```
- `public/grade/grading/form/rubric/lib.php:443`
  ```php
  public function get_definition_copy(gradingform_controller $target) {
  ```
- `public/grade/grading/form/rubric/lib.php:476`
  ```php
  public static function description_form_field_options($context) {
  ```
- `public/grade/grading/form/rubric/lib.php:490`
  ```php
  public function get_formatted_description() {
  ```
- `public/grade/grading/form/rubric/lib.php:515`
  ```php
  public function get_renderer(moodle_page $page) {
  ```
- `public/grade/grading/form/rubric/lib.php:525`
  ```php
  public function render_preview(moodle_page $page) {
  ```
- `public/grade/grading/form/rubric/lib.php:563`
  ```php
  protected function delete_plugin_definition() {
  ```
- `public/grade/grading/form/rubric/lib.php:591`
  ```php
  public function get_or_create_instance($instanceid, $raterid, $itemid) {
  ```
- `public/grade/grading/form/rubric/lib.php:622`
  ```php
  public function render_grade($page, $itemid, $gradinginfo, $defaultcontent, $cangrade) {
  ```
- `public/grade/grading/form/rubric/lib.php:634`
  ```php
  public static function sql_search_from_tables($gdid) {
  ```
- `public/grade/grading/form/rubric/lib.php:649`
  ```php
  public static function sql_search_where($token) {
  ```
- `public/grade/grading/form/rubric/lib.php:671`
  ```php
  public function get_min_max_score() {
  ```
- `public/grade/grading/form/rubric/lib.php:693`
  ```php
  public static function get_external_definition_details() {
  ```
- `public/grade/grading/form/rubric/lib.php:725`
  ```php
  public static function get_external_instance_filling_details() {
  ```
- `public/grade/grading/form/rubric/lib.php:74`
  ```php
  public function extend_settings_navigation(settings_navigation $settingsnav, ?navigation_node $node=null) {
  ```
- `public/grade/grading/form/rubric/lib.php:759`
  ```php
  public function cancel() {
  ```
- `public/grade/grading/form/rubric/lib.php:773`
  ```php
  public function copy($raterid, $itemid) {
  ```
- `public/grade/grading/form/rubric/lib.php:791`
  ```php
  public function is_empty_form($elementvalue) {
  ```
- `public/grade/grading/form/rubric/lib.php:807`
  ```php
  public function clear_attempt($data) {
  ```
- `public/grade/grading/form/rubric/lib.php:822`
  ```php
  public function validate_grading_element($elementvalue) {
  ```
- `public/grade/grading/form/rubric/lib.php:842`
  ```php
  public function get_rubric_filling($force = false) {
  ```
- `public/grade/grading/form/rubric/lib.php:861`
  ```php
  public function update($data) {
  ```
- `public/grade/grading/form/rubric/lib.php:89`
  ```php
  public function extend_navigation(global_navigation $navigation, ?navigation_node $node=null) {
  ```
- `public/grade/grading/form/rubric/lib.php:902`
  ```php
  public function get_grade() {
  ```
- `public/grade/grading/form/rubric/lib.php:943`
  ```php
  public function render_grading_element($page, $gradingformelement) {
  ```
- `public/grade/grading/form/rubric/renderer.php:227`
  ```php
  public function level_template($mode, $options, $elementname = '{NAME}', $criterionid = '{CRITERION-id}', $level = null) {
  ```
- `public/grade/grading/form/rubric/renderer.php:390`
  ```php
  protected function rubric_template($mode, $options, $elementname, $criteriastr) {
  ```
- `public/grade/grading/form/rubric/renderer.php:446`
  ```php
  protected function rubric_edit_options($mode, $options) {
  ```
- `public/grade/grading/form/rubric/renderer.php:522`
  ```php
  public function display_rubric($criteria, $options, $mode, $elementname = null, $values = null) {
  ```
- `public/grade/grading/form/rubric/renderer.php:564`
  ```php
  protected function get_css_class_suffix($idx, $maxidx) {
  ```
- `public/grade/grading/form/rubric/renderer.php:588`
  ```php
  public function display_instances($instances, $defaultcontent, $cangrade) {
  ```
- `public/grade/grading/form/rubric/renderer.php:59`
  ```php
  public function criterion_template($mode, $options, $elementname = '{NAME}', $criterion = null, $levelsstr = '{LEVELS}', $value = null) {
  ```
- `public/grade/grading/form/rubric/renderer.php:608`
  ```php
  public function display_instance(gradingform_rubric_instance $instance, $idx, $cangrade) {
  ```
- `public/grade/grading/form/rubric/renderer.php:635`
  ```php
  public function display_regrade_confirmation($elementname, $changelevel, $value) {
  ```
- `public/grade/grading/form/rubric/renderer.php:658`
  ```php
  public function display_rubric_mapping_explained($scores) {
  ```
- `public/grade/grading/form/rubric/rubriceditor.php:101`
  ```php
  public function toHtml() {
  ```
- `public/grade/grading/form/rubric/rubriceditor.php:153`
  ```php
  protected function prepare_data($value = null, $withvalidation = false) {
  ```
- `public/grade/grading/form/rubric/rubriceditor.php:332`
  ```php
  protected function get_next_id($ids) {
  ```
- `public/grade/grading/form/rubric/rubriceditor.php:351`
  ```php
  public function non_js_button_pressed($value) {
  ```
- `public/grade/grading/form/rubric/rubriceditor.php:366`
  ```php
  public function validate($value) {
  ```
- `public/grade/grading/form/rubric/rubriceditor.php:381`
  ```php
  public function exportValue(&$submitValues, $assoc = false) {
  ```
- `public/grade/grading/form/rubric/rubriceditor.php:63`
  ```php
  public function __construct($elementName=null, $elementLabel=null, $attributes=null) {
  ```
- `public/grade/grading/form/rubric/rubriceditor.php:72`
  ```php
  public function getHelpButton() {
  ```
- `public/grade/grading/form/rubric/rubriceditor.php:81`
  ```php
  public function getElementTemplateType() {
  ```
- `public/grade/grading/form/rubric/rubriceditor.php:92`
  ```php
  public function add_regrade_confirmation($changelevel) {
  ```
- `public/grade/grading/form/rubric/tests/behat/behat_gradingform_rubric.php:234`
  ```php
  public function i_replace_rubric_level_with($currentvalue, $value, $criterionname) {
  ```
- `public/grade/grading/form/rubric/tests/behat/behat_gradingform_rubric.php:286`
  ```php
  public function i_grade_by_filling_the_rubric_with(TableNode $rubric) {
  ```
- `public/grade/grading/form/rubric/tests/behat/behat_gradingform_rubric.php:358`
  ```php
  public function the_level_with_points_was_previously_selected_for_the_rubric_criterion($points, $criterionname) {
  ```
- `public/grade/grading/form/rubric/tests/behat/behat_gradingform_rubric.php:386`
  ```php
  public function the_level_with_points_is_selected_for_the_rubric_criterion($points, $criterionname) {
  ```
- `public/grade/grading/form/rubric/tests/behat/behat_gradingform_rubric.php:416`
  ```php
  public function the_level_with_points_is_not_selected_for_the_rubric_criterion($points, $criterionname) {
  ```
- `public/grade/grading/form/rubric/tests/behat/behat_gradingform_rubric.php:443`
  ```php
  protected function set_rubric_field_value($name, $value, $visible = false) {
  ```
- `public/grade/grading/form/rubric/tests/behat/behat_gradingform_rubric.php:469`
  ```php
  protected function click_and_confirm($node) {
  ```
- `public/grade/grading/form/rubric/tests/behat/behat_gradingform_rubric.php:498`
  ```php
  protected function get_level_xpath($points) {
  ```
- `public/grade/grading/form/rubric/tests/behat/behat_gradingform_rubric.php:512`
  ```php
  protected function get_criterion_xpath($criterionname) {
  ```
- `public/grade/grading/form/rubric/tests/behat/behat_gradingform_rubric.php:68`
  ```php
  public function i_define_the_following_rubric(TableNode $rubric) {
  ```
- `public/grade/grading/form/rubric/tests/generator/criterion.php:111`
  ```php
  public function get_all_level_values(): array {
  ```
- `public/grade/grading/form/rubric/tests/generator/criterion.php:51`
  ```php
  public function __construct(string $description, array $levels = []) {
  ```
- `public/grade/grading/form/rubric/tests/generator/criterion.php:65`
  ```php
  public function add_level(string $definition, int $score): self {
  ```
- `public/grade/grading/form/rubric/tests/generator/criterion.php:79`
  ```php
  public function get_description(): string {
  ```
- `public/grade/grading/form/rubric/tests/generator/criterion.php:88`
  ```php
  public function get_levels(): array {
  ```
- `public/grade/grading/form/rubric/tests/generator/criterion.php:98`
  ```php
  public function get_all_values(int $sortorder): array {
  ```
- `public/grade/grading/form/rubric/tests/generator/lib.php:108`
  ```php
  protected function get_criterion(string $description, array $levels = []): criterion {
  ```
- `public/grade/grading/form/rubric/tests/generator/lib.php:120`
  ```php
  public function get_level_and_criterion_for_values(
  ```
- `public/grade/grading/form/rubric/tests/generator/lib.php:164`
  ```php
  public function get_submitted_form_data(gradingform_rubric_controller $controller, int $itemid, array $values): array {
  ```
- `public/grade/grading/form/rubric/tests/generator/lib.php:191`
  ```php
  public function get_test_rubric(context $context, string $component, string $area): gradingform_rubric_controller {
  ```
- `public/grade/grading/form/rubric/tests/generator/lib.php:219`
  ```php
  public function get_test_form_data(
  ```
- `public/grade/grading/form/rubric/tests/generator/lib.php:55`
  ```php
  public function create_instance(
  ```
- `public/grade/grading/form/rubric/tests/generator/lib.php:95`
  ```php
  protected function get_rubric(string $name, string $description): rubric {
  ```
- `public/grade/grading/form/rubric/tests/generator/rubric.php:100`
  ```php
  public function set_option(string $key, $value): self {
  ```
- `public/grade/grading/form/rubric/tests/generator/rubric.php:111`
  ```php
  public function add_criteria(criterion $criterion): self {
  ```
- `public/grade/grading/form/rubric/tests/generator/rubric.php:122`
  ```php
  protected function get_all_criterion_values(): array {
  ```
- `public/grade/grading/form/rubric/tests/generator/rubric.php:59`
  ```php
  public function __construct(string $name, string $description) {
  ```
- `public/grade/grading/form/rubric/tests/generator/rubric.php:76`
  ```php
  public function get_definition(): stdClass {
  ```
- `public/grade/grading/form/rubric/tests/generator_test.php:154`
  ```php
  public function test_get_level_and_criterion_for_values(): void {
  ```
- `public/grade/grading/form/rubric/tests/generator_test.php:206`
  ```php
  public function test_get_test_rubric(): void {
  ```
- `public/grade/grading/form/rubric/tests/generator_test.php:234`
  ```php
  public function test_get_submitted_form_data(): void {
  ```
- `public/grade/grading/form/rubric/tests/generator_test.php:280`
  ```php
  public function test_get_test_form_data(): void {
  ```
- `public/grade/grading/form/rubric/tests/generator_test.php:46`
  ```php
  public function test_rubric_creation(): void {
  ```
- `public/grade/grading/form/rubric/tests/grades/grader/gradingpanel/external/fetch_test.php:172`
  ```php
  public function test_execute_fetch_graded(): void {
  ```
- `public/grade/grading/form/rubric/tests/grades/grader/gradingpanel/external/fetch_test.php:189`
  ```php
  public function test_execute_fetch_does_not_return_data_to_other_students(): void {
  ```
- `public/grade/grading/form/rubric/tests/grades/grader/gradingpanel/external/fetch_test.php:210`
  ```php
  public function test_execute_fetch_return_data_to_graded_user(): void {
  ```
- `public/grade/grading/form/rubric/tests/grades/grader/gradingpanel/external/fetch_test.php:227`
  ```php
  private function execute_and_assert_fetch($forum, $controller, $definition, $fetcheruser, $grader, $gradeduser) {
  ```
- `public/grade/grading/form/rubric/tests/grades/grader/gradingpanel/external/fetch_test.php:328`
  ```php
  protected function get_forum_instance(array $config = []): forum_entity {
  ```
- `public/grade/grading/form/rubric/tests/grades/grader/gradingpanel/external/fetch_test.php:346`
  ```php
  protected function get_test_data(): array {
  ```
- `public/grade/grading/form/rubric/tests/grades/grader/gradingpanel/external/fetch_test.php:398`
  ```php
  protected function get_test_form_data(
  ```
- `public/grade/grading/form/rubric/tests/grades/grader/gradingpanel/external/fetch_test.php:41`
  ```php
  public function test_execute_invalid_component(): void {
  ```
- `public/grade/grading/form/rubric/tests/grades/grader/gradingpanel/external/fetch_test.php:54`
  ```php
  public function test_execute_invalid_itemname(): void {
  ```
- `public/grade/grading/form/rubric/tests/grades/grader/gradingpanel/external/fetch_test.php:67`
  ```php
  public function test_execute_incorrect_type(): void {
  ```
- `public/grade/grading/form/rubric/tests/grades/grader/gradingpanel/external/fetch_test.php:89`
  ```php
  public function test_execute_fetch_empty(): void {
  ```
- `public/grade/grading/form/rubric/tests/grades/grader/gradingpanel/external/store_test.php:105`
  ```php
  public function test_execute_store_graded(): void {
  ```
- `public/grade/grading/form/rubric/tests/grades/grader/gradingpanel/external/store_test.php:213`
  ```php
  protected function get_forum_instance(array $config = []): forum_entity {
  ```
- `public/grade/grading/form/rubric/tests/grades/grader/gradingpanel/external/store_test.php:231`
  ```php
  protected function get_test_data(): array {
  ```
- `public/grade/grading/form/rubric/tests/grades/grader/gradingpanel/external/store_test.php:40`
  ```php
  public function test_execute_invalid_component(): void {
  ```
- `public/grade/grading/form/rubric/tests/grades/grader/gradingpanel/external/store_test.php:53`
  ```php
  public function test_execute_invalid_itemname(): void {
  ```
- `public/grade/grading/form/rubric/tests/grades/grader/gradingpanel/external/store_test.php:66`
  ```php
  public function test_execute_incorrect_type(): void {
  ```
- `public/grade/grading/form/rubric/tests/grades/grader/gradingpanel/external/store_test.php:86`
  ```php
  public function test_execute_disabled(): void {
  ```
- `public/grade/grading/form/rubric/tests/privacy/provider_test.php:144`
  ```php
  protected function get_test_rubric(context_module $context, string $component, string $area): gradingform_rubric_controller {
  ```
- `public/grade/grading/form/rubric/tests/privacy/provider_test.php:162`
  ```php
  protected function get_test_form_data(
  ```
- `public/grade/grading/form/rubric/tests/privacy/provider_test.php:44`
  ```php
  public function test_get_gradingform_export_data(): void {
  ```
- `public/grade/grading/form/rubric/tests/privacy/provider_test.php:84`
  ```php
  public function test_delete_gradingform_for_instances(): void {
  ```
- `public/grade/grading/lib.php:114`
  ```php
  public function get_context() {
  ```
- `public/grade/grading/lib.php:123`
  ```php
  public function set_context(stdClass $context) {
  ```
- `public/grade/grading/lib.php:133`
  ```php
  public function get_component() {
  ```
- `public/grade/grading/lib.php:142`
  ```php
  public function set_component($component) {
  ```
- `public/grade/grading/lib.php:153`
  ```php
  public function get_area() {
  ```
- `public/grade/grading/lib.php:162`
  ```php
  public function set_area($area) {
  ```
- `public/grade/grading/lib.php:176`
  ```php
  public function get_component_title() {
  ```
- `public/grade/grading/lib.php:209`
  ```php
  public function get_area_title() {
  ```
- `public/grade/grading/lib.php:234`
  ```php
  public function load($areaid) {
  ```
- `public/grade/grading/lib.php:250`
  ```php
  public static function available_methods($includenone = true) {
  ```
- `public/grade/grading/lib.php:276`
  ```php
  public function get_available_methods($includenone = true) {
  ```
- `public/grade/grading/lib.php:290`
  ```php
  public static function available_areas($component) {
  ```
- `public/grade/grading/lib.php:329`
  ```php
  public function get_available_areas() {
  ```
- `public/grade/grading/lib.php:357`
  ```php
  public function get_active_method() {
  ```
- `public/grade/grading/lib.php:385`
  ```php
  public function set_active_method($method) {
  ```
- `public/grade/grading/lib.php:446`
  ```php
  public function extend_settings_navigation(settings_navigation $settingsnav, ?navigation_node $modulenode=null) {
  ```
- `public/grade/grading/lib.php:475`
  ```php
  public function extend_navigation(global_navigation $navigation, ?navigation_node $modulenode=null) {
  ```
- `public/grade/grading/lib.php:47`
  ```php
  function get_grading_manager($context_or_areaid = null, $component = null, $area = null) {
  ```
- `public/grade/grading/lib.php:493`
  ```php
  public function get_controller($method) {
  ```
- `public/grade/grading/lib.php:538`
  ```php
  public function get_active_controller() {
  ```
- `public/grade/grading/lib.php:554`
  ```php
  public function get_management_url(?moodle_url $returnurl = null) {
  ```
- `public/grade/grading/lib.php:583`
  ```php
  public function create_shared_area($method) {
  ```
- `public/grade/grading/lib.php:604`
  ```php
  public static function delete_all_for_context($contextid) {
  ```
- `public/grade/grading/lib.php:638`
  ```php
  public static function tokenize($needle) {
  ```
- `public/grade/grading/lib.php:674`
  ```php
  private function ensure_isset(array $properties) {
  ```
- `public/grade/grading/pick_form.php:41`
  ```php
  public function definition() {
  ```
- `public/grade/grading/renderer.php:44`
  ```php
  public function management_method_selector(grading_manager $manager, moodle_url $targeturl) {
  ```
- `public/grade/grading/renderer.php:65`
  ```php
  public function management_action_icon(moodle_url $url, $text, $icon) {
  ```
- `public/grade/grading/renderer.php:78`
  ```php
  public function management_message($message) {
  ```
- `public/grade/grading/renderer.php:94`
  ```php
  public function pick_action_icon(moodle_url $url, $text, $icon = '', $class = '') {
  ```
- `public/grade/grading/tests/behat/behat_grading.php:111`
  ```php
  public function i_publish_grading_form_definition_as_a_public_template($activityname) {
  ```
- `public/grade/grading/tests/behat/behat_grading.php:127`
  ```php
  public function i_set_activity_to_use_grading_form($activityname, $templatename) {
  ```
- `public/grade/grading/tests/behat/behat_grading.php:159`
  ```php
  public function i_save_the_advanced_grading_form() {
  ```
- `public/grade/grading/tests/behat/behat_grading.php:173`
  ```php
  public function i_complete_the_advanced_grading_form_with_these_values(TableNode $data) {
  ```
- `public/grade/grading/tests/behat/behat_grading.php:48`
  ```php
  public function i_go_to_advanced_grading_page($activityname) {
  ```
- `public/grade/grading/tests/behat/behat_grading.php:66`
  ```php
  public function i_go_to_advanced_grading_definition_page($activityname) {
  ```
- `public/grade/grading/tests/behat/behat_grading.php:87`
  ```php
  public function i_go_to_activity_advanced_grading_page($userfullname, $activityname) {
  ```
- `public/grade/grading/tests/generator/lib.php:48`
  ```php
  public function create_instance(context $context, string $component, string $areaname, string $method): gradingform_controller {
  ```
- `public/grade/grading/tests/generator_test.php:46`
  ```php
  public function test_create_instance(): void {
  ```
- `public/grade/grading/tests/grading_manager_test.php:35`
  ```php
  public function test_basic_instantiation(): void {
  ```
- `public/grade/grading/tests/grading_manager_test.php:53`
  ```php
  public function test_set_and_get_grading_area(): void {
  ```
- `public/grade/grading/tests/grading_manager_test.php:95`
  ```php
  public function test_tokenize(): void {
  ```
- `public/grade/grading/tests/privacy/legacy_polyfill_test.php:103`
  ```php
  protected static function _delete_gradingform_for_instances($instanceids) {
  ```
- `public/grade/grading/tests/privacy/legacy_polyfill_test.php:113`
  ```php
  protected static function _get_metadata(\core_privacy\local\metadata\collection $collection) {
  ```
- `public/grade/grading/tests/privacy/legacy_polyfill_test.php:128`
  ```php
  public function get_return_value() {
  ```
- `public/grade/grading/tests/privacy/legacy_polyfill_test.php:33`
  ```php
  public function test_export_gradingform_instance_data(): void {
  ```
- `public/grade/grading/tests/privacy/legacy_polyfill_test.php:48`
  ```php
  public function test_get_metadata(): void {
  ```
- `public/grade/grading/tests/privacy/legacy_polyfill_test.php:56`
  ```php
  public function test_delete_gradingform_for_instances(): void {
  ```
- `public/grade/grading/tests/privacy/legacy_polyfill_test.php:94`
  ```php
  protected static function _export_gradingform_instance_data(\context $context, $instanceid, $subcontext) {
  ```
- `public/grade/grading/tests/privacy/provider_test.php:106`
  ```php
  public function test_export_user_data_no_content(): void {
  ```
- `public/grade/grading/tests/privacy/provider_test.php:123`
  ```php
  public function test_export_user_data(): void {
  ```
- `public/grade/grading/tests/privacy/provider_test.php:202`
  ```php
  public function test_delete_data_for_all_users_in_context(): void {
  ```
- `public/grade/grading/tests/privacy/provider_test.php:223`
  ```php
  public function test_delete_data_for_user(): void {
  ```
- `public/grade/grading/tests/privacy/provider_test.php:266`
  ```php
  public function test_export_item_data(): void {
  ```
- `public/grade/grading/tests/privacy/provider_test.php:304`
  ```php
  public function test_delete_instance_data(): void {
  ```
- `public/grade/grading/tests/privacy/provider_test.php:357`
  ```php
  public function test_delete_data_for_instances(): void {
  ```
- `public/grade/grading/tests/privacy/provider_test.php:439`
  ```php
  protected function grading_setup_test_scenario_data($defnameprefix = null, $now = null) {
  ```
- `public/grade/grading/tests/privacy/provider_test.php:66`
  ```php
  public function test_get_contexts_for_userid(): void {
  ```
- `public/grade/grading/tests/privacy/provider_test.php:93`
  ```php
  public function test_get_users_in_context(): void {
  ```
- `public/grade/import/csv/classes/load_data.php:105`
  ```php
  public static function fetch_grade_items($courseid) {
  ```
- `public/grade/import/csv/classes/load_data.php:130`
  ```php
  protected function trim_headers() {
  ```
- `public/grade/import/csv/classes/load_data.php:141`
  ```php
  protected function raise_limits() {
  ```
- `public/grade/import/csv/classes/load_data.php:157`
  ```php
  protected function insert_grade_record(stdClass $record, int $studentid, grade_item $gradeitem): mixed {
  ```
- `public/grade/import/csv/classes/load_data.php:189`
  ```php
  protected function import_new_grade_item($header, $key, $value) {
  ```
- `public/grade/import/csv/classes/load_data.php:225`
  ```php
  protected function check_user_exists($value, $userfields) {
  ```
- `public/grade/import/csv/classes/load_data.php:278`
  ```php
  protected function create_feedback($courseid, $itemid, $value) {
  ```
- `public/grade/import/csv/classes/load_data.php:306`
  ```php
  protected function update_grade_item($courseid, $map, $key, $verbosescales, $value, int $linenumber) {
  ```
- `public/grade/import/csv/classes/load_data.php:371`
  ```php
  protected function cleanup_import($notification) {
  ```
- `public/grade/import/csv/classes/load_data.php:390`
  ```php
  protected function map_user_data_with_value(
  ```
- `public/grade/import/csv/classes/load_data.php:470`
  ```php
  public function prepare_import_grade_data($header, $formdata, $csvimport, $courseid, $separatemode, $currentgroup,
  ```
- `public/grade/import/csv/classes/load_data.php:660`
  ```php
  public function get_headers() {
  ```
- `public/grade/import/csv/classes/load_data.php:669`
  ```php
  public function get_error() {
  ```
- `public/grade/import/csv/classes/load_data.php:678`
  ```php
  public function get_iid() {
  ```
- `public/grade/import/csv/classes/load_data.php:687`
  ```php
  public function get_previewdata() {
  ```
- `public/grade/import/csv/classes/load_data.php:696`
  ```php
  public function get_gradebookerrors() {
  ```
- `public/grade/import/csv/classes/load_data.php:71`
  ```php
  public function load_csv_content($text, $encoding, $separator, $previewrows) {
  ```
- `public/grade/import/csv/classes/output/renderer.php:43`
  ```php
  public function standard_upload_file_form($course, $mform) {
  ```
- `public/grade/import/csv/classes/output/renderer.php:65`
  ```php
  public function import_preview_page($header, $data) {
  ```
- `public/grade/import/csv/classes/output/renderer.php:85`
  ```php
  public function errors($errors) {
  ```
- `public/grade/import/csv/classes/privacy/provider.php:43`
  ```php
  public static function get_reason(): string {
  ```
- `public/grade/import/csv/tests/fixtures/phpunit_gradeimport_csv_load_data.php:124`
  ```php
  public function test_map_user_data_with_value(
  ```
- `public/grade/import/csv/tests/fixtures/phpunit_gradeimport_csv_load_data.php:32`
  ```php
  public function test_insert_grade_record($record, $studentid, grade_item $gradeitem) {
  ```
- `public/grade/import/csv/tests/fixtures/phpunit_gradeimport_csv_load_data.php:40`
  ```php
  public function get_importcode() {
  ```
- `public/grade/import/csv/tests/fixtures/phpunit_gradeimport_csv_load_data.php:52`
  ```php
  public function test_import_new_grade_item($header, $key, $value) {
  ```
- `public/grade/import/csv/tests/fixtures/phpunit_gradeimport_csv_load_data.php:65`
  ```php
  public function test_check_user_exists($value, $userfields) {
  ```
- `public/grade/import/csv/tests/fixtures/phpunit_gradeimport_csv_load_data.php:77`
  ```php
  public function test_create_feedback($courseid, $itemid, $value) {
  ```
- `public/grade/import/csv/tests/fixtures/phpunit_gradeimport_csv_load_data.php:92`
  ```php
  public function test_update_grade_item(
  ```
- `public/grade/import/csv/tests/load_data_test.php:150`
  ```php
  public function test_fetch_grade_items(): void {
  ```
- `public/grade/import/csv/tests/load_data_test.php:178`
  ```php
  public function test_insert_grade_record(): void {
  ```
- `public/grade/import/csv/tests/load_data_test.php:217`
  ```php
  public function test_import_new_grade_item(): void {
  ```
- `public/grade/import/csv/tests/load_data_test.php:245`
  ```php
  public static function check_user_exists_provider(): array {
  ```
- `public/grade/import/csv/tests/load_data_test.php:292`
  ```php
  public function test_check_user_exists($field, $value, $successexpected, $allowaccountssameemail = 0): void {
  ```
- `public/grade/import/csv/tests/load_data_test.php:362`
  ```php
  public function test_create_feedback(): void {
  ```
- `public/grade/import/csv/tests/load_data_test.php:378`
  ```php
  public function test_update_grade_item(): void {
  ```
- `public/grade/import/csv/tests/load_data_test.php:413`
  ```php
  public function test_map_user_data_with_value(): void {
  ```
- `public/grade/import/csv/tests/load_data_test.php:512`
  ```php
  public function test_prepare_import_grade_data(): void {
  ```
- `public/grade/import/csv/tests/load_data_test.php:565`
  ```php
  public function test_force_import_option(): void {
  ```
- `public/grade/import/csv/tests/load_data_test.php:60`
  ```php
  public function tearDown(): void {
  ```
- `public/grade/import/csv/tests/load_data_test.php:71`
  ```php
  protected function csv_load($content) {
  ```
- `public/grade/import/csv/tests/load_data_test.php:90`
  ```php
  public function test_load_csv_content(): void {
  ```
- `public/grade/import/direct/classes/import_form.php:35`
  ```php
  public function definition() {
  ```
- `public/grade/import/direct/classes/mapping_form.php:36`
  ```php
  public function definition() {
  ```
- `public/grade/import/direct/classes/privacy/provider.php:43`
  ```php
  public static function get_reason(): string {
  ```
- `public/grade/import/grade_import_form.php:26`
  ```php
  function definition(){
  ```
- `public/grade/import/grade_import_form.php:89`
  ```php
  function definition() {
  ```
- `public/grade/import/key_form.php:35`
  ```php
  function definition() {
  ```
- `public/grade/import/lib.php:182`
  ```php
  function get_unenrolled_users_in_import($importcode, $courseid) {
  ```
- `public/grade/import/lib.php:227`
  ```php
  function import_cleanup($importcode) {
  ```
- `public/grade/import/lib.php:24`
  ```php
  function get_new_importcode() {
  ```
- `public/grade/import/lib.php:46`
  ```php
  function grade_import_commit($courseid, $importcode, $importfeedback=true, $verbose=true) {
  ```
- `public/grade/import/xml/classes/privacy/provider.php:43`
  ```php
  public static function get_reason(): string {
  ```
- `public/grade/import/xml/grade_import_form.php:25`
  ```php
  function definition() {
  ```
- `public/grade/import/xml/grade_import_form.php:97`
  ```php
  function validation($data, $files) {
  ```
- `public/grade/import/xml/lib.php:22`
  ```php
  function import_xml_grades($text, $course, &$error) {
  ```
- `public/grade/lib.php:1101`
  ```php
  public function __construct($params = []) {
  ```
- `public/grade/lib.php:1144`
  ```php
  public function get_options() {
  ```
- `public/grade/lib.php:1182`
  ```php
  public function get_return_url($default, $extras=null) {
  ```
- `public/grade/lib.php:118`
  ```php
  public function __construct($course, $grade_items=null, $groupid=0,
  ```
- `public/grade/lib.php:1226`
  ```php
  public function get_form_fields() {
  ```
- `public/grade/lib.php:1268`
  ```php
  public function add_mform_elements(&$mform) {
  ```
- `public/grade/lib.php:1308`
  ```php
  public function add_url_params(moodle_url $url): moodle_url {
  ```
- `public/grade/lib.php:1349`
  ```php
  function grade_build_nav($path, $pagename=null, $id=null) {
  ```
- `public/grade/lib.php:137`
  ```php
  public function init() {
  ```
- `public/grade/lib.php:1472`
  ```php
  public function get_grade_analysis_url(grade_grade $grade) {
  ```
- `public/grade/lib.php:1531`
  ```php
  public function get_grade_analysis_link(grade_grade $grade): ?string {
  ```
- `public/grade/lib.php:1548`
  ```php
  public function get_grade_action_menu(grade_grade $grade): string {
  ```
- `public/grade/lib.php:1581`
  ```php
  public function get_grade_eid($grade_grade) {
  ```
- `public/grade/lib.php:1594`
  ```php
  public function get_item_eid($grade_item) {
  ```
- `public/grade/lib.php:1606`
  ```php
  public function get_params_for_iconstr($element) {
  ```
- `public/grade/lib.php:1640`
  ```php
  public function get_reset_weights_link(array $element, object $gpr): ?string {
  ```
- `public/grade/lib.php:1669`
  ```php
  public function get_delete_link(array $element, object $gpr): ?string {
  ```
- `public/grade/lib.php:1711`
  ```php
  public function get_duplicate_link(array $element, object $gpr): ?string {
  ```
- `public/grade/lib.php:1736`
  ```php
  public function get_edit_link(array $element, object $gpr): ?string {
  ```
- `public/grade/lib.php:1804`
  ```php
  public function get_advanced_grading_link(array $element, object $gpr): ?string {
  ```
- `public/grade/lib.php:1862`
  ```php
  public function get_hiding_link(array $element, object $gpr): ?string {
  ```
- `public/grade/lib.php:1905`
  ```php
  public function get_locking_link(array $element, object $gpr): ?string {
  ```
- `public/grade/lib.php:1971`
  ```php
  public function get_edit_calculation_link(array $element, object $gpr): ?string {
  ```
- `public/grade/lib.php:1997`
  ```php
  public function set_grade_status_icons(array $element): ?string {
  ```
- `public/grade/lib.php:2048`
  ```php
  public function get_cell_action_menu(array $element, string $mode, grade_plugin_return $gpr,
  ```
- `public/grade/lib.php:2213`
  ```php
  public function get_sorting_link(moodle_url $sortlink, object $gpr, string $direction = 'asc'): string {
  ```
- `public/grade/lib.php:2252`
  ```php
  public function __construct($courseid, $category_grade_last=false, $nooutcomes=false) {
  ```
- `public/grade/lib.php:2277`
  ```php
  public function flatten(&$element, $category_grade_last, $nooutcomes) {
  ```
- `public/grade/lib.php:2323`
  ```php
  public function locate_element($eid) {
  ```
- `public/grade/lib.php:2409`
  ```php
  public function __construct($courseid, $fillers=true, $category_grade_last=false,
  ```
- `public/grade/lib.php:2462`
  ```php
  public function category_collapse(&$element, $collapsed) {
  ```
- `public/grade/lib.php:2493`
  ```php
  public function no_outcomes(&$element) {
  ```
- `public/grade/lib.php:2515`
  ```php
  public function category_grade_last(&$element) {
  ```
- `public/grade/lib.php:2542`
  ```php
  public function fill_levels(&$levels, &$element, $depth) {
  ```
- `public/grade/lib.php:2581`
  ```php
  public static function can_output_item($element) {
  ```
- `public/grade/lib.php:2618`
  ```php
  public function inject_fillers(&$element, $depth) {
  ```
- `public/grade/lib.php:2667`
  ```php
  public function inject_colspans(&$element) {
  ```
- `public/grade/lib.php:2688`
  ```php
  public function locate_element($eid) {
  ```
- `public/grade/lib.php:268`
  ```php
  public function next_user() {
  ```
- `public/grade/lib.php:2745`
  ```php
  public function exporttoxml($root=null, $tabs="\t") {
  ```
- `public/grade/lib.php:2796`
  ```php
  public function exporttojson($root=null, $tabs="\t") {
  ```
- `public/grade/lib.php:2853`
  ```php
  public function get_levels() {
  ```
- `public/grade/lib.php:2862`
  ```php
  public function get_items() {
  ```
- `public/grade/lib.php:2873`
  ```php
  public function get_item($itemid) {
  ```
- `public/grade/lib.php:2889`
  ```php
  function grade_button($type, $courseid, $object) {
  ```
- `public/grade/lib.php:2916`
  ```php
  function grade_extend_settings($plugininfo, $courseid) {
  ```
- `public/grade/lib.php:3053`
  ```php
  public static function get_plugin_strings() {
  ```
- `public/grade/lib.php:3073`
  ```php
  public static function get_aggregation_strings() {
  ```
- `public/grade/lib.php:3096`
  ```php
  public static function get_info_manage_settings($courseid) {
  ```
- `public/grade/lib.php:3124`
  ```php
  public static function get_plugins_reports($courseid) {
  ```
- `public/grade/lib.php:3186`
  ```php
  public static function get_info_scales($courseid) {
  ```
- `public/grade/lib.php:3203`
  ```php
  public static function get_info_outcomes($courseid) {
  ```
- `public/grade/lib.php:3240`
  ```php
  public static function get_info_letters($courseid) {
  ```
- `public/grade/lib.php:3269`
  ```php
  public static function get_plugins_import($courseid) {
  ```
- `public/grade/lib.php:3309`
  ```php
  public static function get_plugins_export($courseid) {
  ```
- `public/grade/lib.php:3355`
  ```php
  public static function get_user_field_value($user, $field) {
  ```
- `public/grade/lib.php:3376`
  ```php
  public static function get_user_profile_fields($courseid, $includecustomfields = false) {
  ```
- `public/grade/lib.php:340`
  ```php
  public function close() {
  ```
- `public/grade/lib.php:3443`
  ```php
  public static function fetch_all_natural_weights_for_course($courseid) {
  ```
- `public/grade/lib.php:3459`
  ```php
  public static function reset_caches() {
  ```
- `public/grade/lib.php:3480`
  ```php
  public static function get_element_icon(array $element, bool $spacerifnone = false): string {
  ```
- `public/grade/lib.php:357`
  ```php
  public function require_active_enrolment($onlyactive = true) {
  ```
- `public/grade/lib.php:3586`
  ```php
  public static function get_element_type_string(array $element): string {
  ```
- `public/grade/lib.php:3636`
  ```php
  public static function get_element_header(array $element, bool $withlink = false, bool $icon = true,
  ```
- `public/grade/lib.php:3693`
  ```php
  public static function get_activity_link(array $element): ?moodle_url {
  ```
- `public/grade/lib.php:370`
  ```php
  public function allow_user_custom_fields($allow = true) {
  ```
- `public/grade/lib.php:385`
  ```php
  private function _push($grade) {
  ```
- `public/grade/lib.php:395`
  ```php
  private function _pop() {
  ```
- `public/grade/lib.php:428`
  ```php
  function print_graded_users_selector($course, $actionpage, $userid=0, $groupid=0, $includeall=true, $return=false) {
  ```
- `public/grade/lib.php:444`
  ```php
  function grade_get_graded_users_select($report, $course, $userid, $groupid, $includeall) {
  ```
- `public/grade/lib.php:497`
  ```php
  function hide_natural_aggregation_upgrade_notice($courseid) {
  ```
- `public/grade/lib.php:506`
  ```php
  function grade_hide_min_max_grade_upgrade_notice($courseid) {
  ```
- `public/grade/lib.php:517`
  ```php
  function grade_upgrade_use_min_max_from_grade_grade($courseid) {
  ```
- `public/grade/lib.php:532`
  ```php
  function grade_upgrade_use_min_max_from_grade_item($courseid) {
  ```
- `public/grade/lib.php:545`
  ```php
  function hide_aggregatesubcats_upgrade_notice($courseid) {
  ```
- `public/grade/lib.php:554`
  ```php
  function hide_gradebook_calculations_freeze_notice($courseid) {
  ```
- `public/grade/lib.php:568`
  ```php
  function print_natural_aggregation_upgrade_notice($courseid, $context, $thispage, $return=false) {
  ```
- `public/grade/lib.php:740`
  ```php
  function grade_get_plugin_info($courseid, $active_type, $active_plugin) {
  ```
- `public/grade/lib.php:815`
  ```php
  function get_gradable_users(int $courseid, ?int $groupid = null, bool $onlyactiveenrol = false): array {
  ```
- `public/grade/lib.php:876`
  ```php
  public function __construct($id, $link, $string, $parent=null) {
  ```
- `public/grade/lib.php:906`
  ```php
  function print_grade_page_head(int $courseid, string $active_type, ?string $active_plugin = null, string|bool $heading = false,
  ```
- `public/grade/penalty/duedate/classes/output/edit_penalty_rule_action_bar.php:31`
  ```php
  public function get_template(): string {
  ```
- `public/grade/penalty/duedate/classes/output/edit_penalty_rule_action_bar.php:36`
  ```php
  public function export_for_template(\renderer_base $output): array {
  ```
- `public/grade/penalty/duedate/classes/output/form/edit_penalty_form.php:120`
  ```php
  public function validation($data, $files): array {
  ```
- `public/grade/penalty/duedate/classes/output/form/edit_penalty_form.php:211`
  ```php
  public function save_data($data): void {
  ```
- `public/grade/penalty/duedate/classes/output/form/edit_penalty_form.php:277`
  ```php
  private static function rule_element(MoodleQuickForm $mform): array {
  ```
- `public/grade/penalty/duedate/classes/output/form/edit_penalty_form.php:47`
  ```php
  public function definition(): void {
  ```
- `public/grade/penalty/duedate/classes/output/view_penalty_rule_action_bar.php:46`
  ```php
  public function __construct(\context $context, string $title, url $url) {
  ```
- `public/grade/penalty/duedate/classes/output/view_penalty_rule_action_bar.php:53`
  ```php
  public function get_template(): string {
  ```
- `public/grade/penalty/duedate/classes/output/view_penalty_rule_action_bar.php:58`
  ```php
  public function export_for_template(\renderer_base $output): array {
  ```
- `public/grade/penalty/duedate/classes/penalty_calculator.php:38`
  ```php
  public static function calculate_penalty(penalty_container $container): void {
  ```
- `public/grade/penalty/duedate/classes/penalty_calculator.php:56`
  ```php
  public static function get_penalty_from_rules(cm_info $cm, int $submissiondate, int $duedate): float {
  ```
- `public/grade/penalty/duedate/classes/penalty_calculator.php:96`
  ```php
  private static function find_effective_penalty_rules(cm_info $cm): array {
  ```
- `public/grade/penalty/duedate/classes/penalty_rule.php:113`
  ```php
  public static function reset_rules(int $contextid): void {
  ```
- `public/grade/penalty/duedate/classes/penalty_rule.php:139`
  ```php
  public static function is_overridden(int $contextid): bool {
  ```
- `public/grade/penalty/duedate/classes/penalty_rule.php:155`
  ```php
  public static function is_inherited(int $contextid): bool {
  ```
- `public/grade/penalty/duedate/classes/penalty_rule.php:38`
  ```php
  protected static function define_properties(): array {
  ```
- `public/grade/penalty/duedate/classes/penalty_rule.php:66`
  ```php
  protected function validate_overdueby($value): bool|lang_string {
  ```
- `public/grade/penalty/duedate/classes/penalty_rule.php:79`
  ```php
  protected function validate_penalty($value): bool|lang_string {
  ```
- `public/grade/penalty/duedate/classes/penalty_rule.php:95`
  ```php
  public static function get_rules(int $contextid): array {
  ```
- `public/grade/penalty/duedate/classes/privacy/provider.php:115`
  ```php
  public static function get_users_in_context(userlist $userlist): void {
  ```
- `public/grade/penalty/duedate/classes/privacy/provider.php:135`
  ```php
  public static function delete_data_for_users(approved_userlist $userlist): void {
  ```
- `public/grade/penalty/duedate/classes/privacy/provider.php:149`
  ```php
  public static function get_metadata(collection $collection): collection {
  ```
- `public/grade/penalty/duedate/classes/privacy/provider.php:165`
  ```php
  protected static function delete_user_data(int $userid): void {
  ```
- `public/grade/penalty/duedate/classes/privacy/provider.php:45`
  ```php
  public static function get_contexts_for_userid(int $userid): contextlist {
  ```
- `public/grade/penalty/duedate/classes/privacy/provider.php:62`
  ```php
  public static function export_user_data(approved_contextlist $contextlist): void {
  ```
- `public/grade/penalty/duedate/classes/privacy/provider.php:83`
  ```php
  public static function delete_data_for_all_users_in_context(context $context): void {
  ```
- `public/grade/penalty/duedate/classes/privacy/provider.php:95`
  ```php
  public static function delete_data_for_user(approved_contextlist $contextlist): void {
  ```
- `public/grade/penalty/duedate/classes/table/penalty_rule_table.php:106`
  ```php
  protected function get_sql($count = false): string {
  ```
- `public/grade/penalty/duedate/classes/table/penalty_rule_table.php:127`
  ```php
  public function col_overdueby($row): string {
  ```
- `public/grade/penalty/duedate/classes/table/penalty_rule_table.php:149`
  ```php
  public function col_penalty($row): string {
  ```
- `public/grade/penalty/duedate/classes/table/penalty_rule_table.php:40`
  ```php
  public function __construct($uniqueid, $contextid) {
  ```
- `public/grade/penalty/duedate/classes/table/penalty_rule_table.php:65`
  ```php
  public function query_db($pagesize, $useinitialsbar = true): void {
  ```
- `public/grade/penalty/duedate/db/install.php:30`
  ```php
  function xmldb_gradepenalty_duedate_install(): void {
  ```
- `public/grade/penalty/duedate/lib.php:35`
  ```php
  function gradepenalty_duedate_extend_navigation_course(navigation_node $navigation, stdClass $course, context $context): void {
  ```
- `public/grade/penalty/duedate/lib.php:50`
  ```php
  function gradepenalty_duedate_extend_navigation_module(navigation_node $navigation, cm_info $cm): void {
  ```
- `public/grade/penalty/duedate/lib.php:65`
  ```php
  function gradepenalty_duedate_output_fragment_penalty_rule_form(array $args): string {
  ```
- `public/grade/penalty/duedate/lib.php:85`
  ```php
  function gradepenalty_duedate_get_settings_url(): url {
  ```
- `public/grade/penalty/duedate/tests/classes/penalty_testcase.php:35`
  ```php
  public function setUp(): void {
  ```
- `public/grade/penalty/duedate/tests/classes/penalty_testcase.php:45`
  ```php
  public function create_sample_rules(?int $contextid = null): void {
  ```
- `public/grade/penalty/duedate/tests/penalty_calculator_test.php:121`
  ```php
  public function test_find_effective_penalty_rules(): void {
  ```
- `public/grade/penalty/duedate/tests/penalty_calculator_test.php:40`
  ```php
  public static function calculate_penalty_provider(): array {
  ```
- `public/grade/penalty/duedate/tests/penalty_calculator_test.php:75`
  ```php
  public function test_calculate_penalty(int $submissiondate, int $duedate, int $expectedgrade): void {
  ```
- `public/grade/penalty/duedate/tests/penalty_rule_persistent_test.php:105`
  ```php
  public function test_is_inherited(): void {
  ```
- `public/grade/penalty/duedate/tests/penalty_rule_persistent_test.php:35`
  ```php
  public function test_get_rules(): void {
  ```
- `public/grade/penalty/duedate/tests/penalty_rule_persistent_test.php:70`
  ```php
  public function test_reset_rules(): void {
  ```
- `public/grade/penalty/duedate/tests/penalty_rule_persistent_test.php:84`
  ```php
  public function test_is_overridden(): void {
  ```
- `public/grade/querylib.php:137`
  ```php
  function grade_get_course_grade($userid, $courseid_or_ids=null) {
  ```
- `public/grade/querylib.php:250`
  ```php
  function grade_get_grade_items_for_activity($cm, $only_main_item=false) {
  ```
- `public/grade/querylib.php:281`
  ```php
  function grade_is_user_graded_in_activity($cm, $userid) {
  ```
- `public/grade/querylib.php:317`
  ```php
  function grade_get_gradable_activities($courseid, $modulename='') {
  ```
- `public/grade/querylib.php:34`
  ```php
  function grade_get_course_grades($courseid, $userid_or_ids=null) {
  ```
- `public/grade/renderer.php:136`
  ```php
  public function initials_selector(
  ```
- `public/grade/renderer.php:186`
  ```php
  public function user_heading(stdClass $user, int $courseid, bool $showbuttons = true): string {
  ```
- `public/grade/renderer.php:247`
  ```php
  public function render_penalty_indicator(penalty_indicator $penaltyindicator): string {
  ```
- `public/grade/renderer.php:40`
  ```php
  public function render_action_bar(action_bar $actionbar): string {
  ```
- `public/grade/renderer.php:59`
  ```php
  public function group_selector(object $course, ?string $groupactionbaseurl = null): ?string {
  ```
- `public/grade/report/grader/classes/event/grade_report_viewed.php:44`
  ```php
  public static function get_name() {
  ```
- `public/grade/report/grader/classes/external/get_users_in_report.php:126`
  ```php
  public static function execute_returns(): external_single_structure {
  ```
- `public/grade/report/grader/classes/external/get_users_in_report.php:51`
  ```php
  public static function execute_parameters(): external_function_parameters {
  ```
- `public/grade/report/grader/classes/external/get_users_in_report.php:65`
  ```php
  public static function execute(int $courseid): array {
  ```
- `public/grade/report/grader/classes/output/action_bar.php:46`
  ```php
  public function __construct(\context_course $context) {
  ```
- `public/grade/report/grader/classes/output/action_bar.php:63`
  ```php
  public function get_template(): string {
  ```
- `public/grade/report/grader/classes/output/action_bar.php:74`
  ```php
  public function export_for_template(\renderer_base $output): array {
  ```
- `public/grade/report/grader/classes/privacy/provider.php:52`
  ```php
  public static function get_metadata(collection $items): collection {
  ```
- `public/grade/report/grader/classes/privacy/provider.php:82`
  ```php
  public static function export_user_preferences(int $userid) {
  ```
- `public/grade/report/grader/db/upgrade.php:31`
  ```php
  function xmldb_gradereport_grader_upgrade(int $oldversion): bool {
  ```
- `public/grade/report/grader/lib.php:1248`
  ```php
  protected function format_average_cell(grade_item $gradeitem, ?array $aggr = null, ?bool $shownumberofgrades = null): html_table_cell {
  ```
- `public/grade/report/grader/lib.php:1292`
  ```php
  public function get_grade_table($displayaverages = false) {
  ```
- `public/grade/report/grader/lib.php:1329`
  ```php
  public function get_left_range_row($rows=array(), $colspan=1) {
  ```
- `public/grade/report/grader/lib.php:1355`
  ```php
  public function get_left_avg_row($rows=array(), $colspan=1, $groupavg=false) {
  ```
- `public/grade/report/grader/lib.php:1404`
  ```php
  public function get_right_range_row($rows=array()) {
  ```
- `public/grade/report/grader/lib.php:142`
  ```php
  public function __construct($courseid, $gpr, $context, $page=null, $sortitemid=null, string $sort = '') {
  ```
- `public/grade/report/grader/lib.php:1442`
  ```php
  protected function get_course_header($element) {
  ```
- `public/grade/report/grader/lib.php:1472`
  ```php
  public function process_action($target, $action) {
  ```
- `public/grade/report/grader/lib.php:1487`
  ```php
  protected static function filter_collapsed_categories($courseid, $collapsed) {
  ```
- `public/grade/report/grader/lib.php:1511`
  ```php
  protected static function get_collapsed_preferences($courseid) {
  ```
- `public/grade/report/grader/lib.php:1564`
  ```php
  protected static function set_collapsed_preferences($courseid, $collapsed) {
  ```
- `public/grade/report/grader/lib.php:1596`
  ```php
  public static function do_process_action($target, $action, $courseid = null) {
  ```
- `public/grade/report/grader/lib.php:1668`
  ```php
  public function get_sort_arrows(array $extrafields = []) {
  ```
- `public/grade/report/grader/lib.php:1735`
  ```php
  public function get_students_per_page(): int {
  ```
- `public/grade/report/grader/lib.php:1750`
  ```php
  public function get_category_view_mode_link(moodle_url $url, string $title, string $action, bool $active = false): ?string {
  ```
- `public/grade/report/grader/lib.php:1765`
  ```php
  public function get_hide_show_link(): string {
  ```
- `public/grade/report/grader/lib.php:1780`
  ```php
  public function get_default_sortable(): string {
  ```
- `public/grade/report/grader/lib.php:1796`
  ```php
  public function get_cell_display_class(grade_item $item): string {
  ```
- `public/grade/report/grader/lib.php:1856`
  ```php
  function gradereport_grader_get_report_link(context_course $context, int $courseid,
  ```
- `public/grade/report/grader/lib.php:192`
  ```php
  public function process_data($data) {
  ```
- `public/grade/report/grader/lib.php:353`
  ```php
  private function setup_sortitemid(string $sort = '') {
  ```
- `public/grade/report/grader/lib.php:408`
  ```php
  public function load_users(bool $allusers = false) {
  ```
- `public/grade/report/grader/lib.php:530`
  ```php
  protected function get_allgradeitems() {
  ```
- `public/grade/report/grader/lib.php:551`
  ```php
  public function get_max_students_per_page(): int {
  ```
- `public/grade/report/grader/lib.php:573`
  ```php
  public function load_final_grades() {
  ```
- `public/grade/report/grader/lib.php:644`
  ```php
  public function get_left_rows($displayaverages) {
  ```
- `public/grade/report/grader/lib.php:802`
  ```php
  public function get_right_rows(bool $displayaverages): array {
  ```
- `public/grade/report/grader/preferences_form.php:186`
  ```php
  function validation($data, $files) {
  ```
- `public/grade/report/grader/preferences_form.php:37`
  ```php
  function definition() {
  ```
- `public/grade/report/grader/tests/behat/behat_gradereport_grader.php:38`
  ```php
  protected function get_user_id($name) {
  ```
- `public/grade/report/grader/tests/behat/behat_gradereport_grader.php:54`
  ```php
  public function i_click_on_user_menu(string $student) {
  ```
- `public/grade/report/grader/tests/behat/behat_gradereport_grader.php:68`
  ```php
  protected function get_user_selector(string $student): string {
  ```
- `public/grade/report/grader/tests/behat/behat_gradereport_grader.php:80`
  ```php
  public function i_click_on_user_profile_field_menu(string $field) {
  ```
- `public/grade/report/grader/tests/behat/behat_gradereport_grader.php:91`
  ```php
  public static function get_partial_named_selectors(): array {
  ```
- `public/grade/report/grader/tests/external/get_users_in_report_test.php:38`
  ```php
  public function test_execute(): void {
  ```
- `public/grade/report/grader/tests/privacy/provider_test.php:46`
  ```php
  public function setUp(): void {
  ```
- `public/grade/report/grader/tests/privacy/provider_test.php:55`
  ```php
  public function test_export_user_preferences_not_defined(): void {
  ```
- `public/grade/report/grader/tests/privacy/provider_test.php:67`
  ```php
  public function test_export_user_preferences_single(): void {
  ```
- `public/grade/report/grader/tests/privacy/provider_test.php:93`
  ```php
  public function test_export_user_preferences_multiple(): void {
  ```
- `public/grade/report/history/classes/event/grade_report_viewed.php:44`
  ```php
  public static function get_name() {
  ```
- `public/grade/report/history/classes/filter_form.php:46`
  ```php
  public function definition() {
  ```
- `public/grade/report/history/classes/helper.php:113`
  ```php
  public static function get_users_count($context, $search = '') {
  ```
- `public/grade/report/history/classes/helper.php:130`
  ```php
  protected static function get_users_sql_and_params($context, $search = '', $count = false) {
  ```
- `public/grade/report/history/classes/helper.php:193`
  ```php
  public static function get_graders($courseid) {
  ```
- `public/grade/report/history/classes/helper.php:47`
  ```php
  public static function init_js($courseid, ?array $currentusers = null) {
  ```
- `public/grade/report/history/classes/helper.php:95`
  ```php
  public static function get_users($context, $search = '', $page = 0, $perpage = 25) {
  ```
- `public/grade/report/history/classes/output/renderer.php:48`
  ```php
  protected function render_user_button(user_button $button) {
  ```
- `public/grade/report/history/classes/output/renderer.php:60`
  ```php
  protected function render_tablelog(tablelog $tablelog) {
  ```
- `public/grade/report/history/classes/output/tablelog.php:131`
  ```php
  protected function define_table_configs(\moodle_url $url) {
  ```
- `public/grade/report/history/classes/output/tablelog.php:152`
  ```php
  protected function define_table_filters(\stdClass $filters): void {
  ```
- `public/grade/report/history/classes/output/tablelog.php:180`
  ```php
  protected function define_table_columns() {
  ```
- `public/grade/report/history/classes/output/tablelog.php:222`
  ```php
  public function col_country(\stdClass $history): string {
  ```
- `public/grade/report/history/classes/output/tablelog.php:234`
  ```php
  public function col_finalgrade(\stdClass $history) {
  ```
- `public/grade/report/history/classes/output/tablelog.php:251`
  ```php
  public function col_prevgrade(\stdClass $history) {
  ```
- `public/grade/report/history/classes/output/tablelog.php:268`
  ```php
  public function col_timemodified(\stdClass $history) {
  ```
- `public/grade/report/history/classes/output/tablelog.php:279`
  ```php
  public function col_itemname(\stdClass $history) {
  ```
- `public/grade/report/history/classes/output/tablelog.php:302`
  ```php
  public function col_grader(\stdClass $history) {
  ```
- `public/grade/report/history/classes/output/tablelog.php:329`
  ```php
  public function col_overridden(\stdClass $history) {
  ```
- `public/grade/report/history/classes/output/tablelog.php:340`
  ```php
  public function col_locked(\stdClass $history) {
  ```
- `public/grade/report/history/classes/output/tablelog.php:351`
  ```php
  public function col_excluded(\stdClass $history) {
  ```
- `public/grade/report/history/classes/output/tablelog.php:362`
  ```php
  public function col_feedback(\stdClass $history) {
  ```
- `public/grade/report/history/classes/output/tablelog.php:388`
  ```php
  protected function get_filters_sql_and_params() {
  ```
- `public/grade/report/history/classes/output/tablelog.php:440`
  ```php
  protected function get_sql_and_params($count = false) {
  ```
- `public/grade/report/history/classes/output/tablelog.php:535`
  ```php
  public function get_sql_sort() {
  ```
- `public/grade/report/history/classes/output/tablelog.php:551`
  ```php
  public function query_db($pagesize, $useinitialsbar = true) {
  ```
- `public/grade/report/history/classes/output/tablelog.php:577`
  ```php
  public function get_selected_users(): array {
  ```
- `public/grade/report/history/classes/output/tablelog.php:96`
  ```php
  public function __construct($uniqueid, \context_course $context, $url, $filters = array(), $download = '', $page = 0,
  ```
- `public/grade/report/history/classes/output/user_button.php:47`
  ```php
  public function __construct(\moodle_url $url, $label, $method = 'post') {
  ```
- `public/grade/report/history/classes/privacy/provider.php:43`
  ```php
  public static function get_reason(): string {
  ```
- `public/grade/report/history/db/upgrade.php:31`
  ```php
  function xmldb_gradereport_history_upgrade($oldversion) {
  ```
- `public/grade/report/history/tests/report_test.php:197`
  ```php
  public function test_get_users(): void {
  ```
- `public/grade/report/history/tests/report_test.php:254`
  ```php
  public static function get_users_with_profile_fields_provider(): array {
  ```
- `public/grade/report/history/tests/report_test.php:324`
  ```php
  public function test_get_users_with_profile_fields(string $showuseridentity, string $searchstring,
  ```
- `public/grade/report/history/tests/report_test.php:33`
  ```php
  public function test_query_db(): void {
  ```
- `public/grade/report/history/tests/report_test.php:386`
  ```php
  public static function get_users_provider(): array {
  ```
- `public/grade/report/history/tests/report_test.php:421`
  ```php
  public function test_get_users_with_groups($groupmode, $teacherrole, $teachergroups, $expectedusers): void {
  ```
- `public/grade/report/history/tests/report_test.php:498`
  ```php
  public function test_graders(): void {
  ```
- `public/grade/report/history/tests/report_test.php:564`
  ```php
  public function test_grade_history_with_different_sources(): void {
  ```
- `public/grade/report/history/tests/report_test.php:621`
  ```php
  protected function assertGradeHistoryIds(array $expectedids, array $objects) {
  ```
- `public/grade/report/history/tests/report_test.php:637`
  ```php
  protected function create_grade_history($params) {
  ```
- `public/grade/report/history/tests/report_test.php:680`
  ```php
  protected function get_tablelog_results($coursecontext, $filters = array(), $count = false) {
  ```
- `public/grade/report/history/tests/report_test.php:698`
  ```php
  public function get_test_results($count = false) {
  ```
- `public/grade/report/lib.php:190`
  ```php
  public function __construct($courseid, $gpr, $context, $page=null) {
  ```
- `public/grade/report/lib.php:231`
  ```php
  public function get_pref($pref, $objectid=null) {
  ```
- `public/grade/report/lib.php:278`
  ```php
  public function set_pref($pref, $pref_value='default', $itemid=null) {
  ```
- `public/grade/report/lib.php:314`
  ```php
  public static function get_additional_context(context_course $context, int $courseid, array $element,
  ```
- `public/grade/report/lib.php:341`
  ```php
  public function get_numusers($groups = true, $users = false) {
  ```
- `public/grade/report/lib.php:408`
  ```php
  public static function supports_mygrades() {
  ```
- `public/grade/report/lib.php:415`
  ```php
  protected function setup_groups() {
  ```
- `public/grade/report/lib.php:442`
  ```php
  public function setup_users() {
  ```
- `public/grade/report/lib.php:482`
  ```php
  protected function get_sort_arrow(string $direction = 'down', ?moodle_url $sortlink = null) {
  ```
- `public/grade/report/lib.php:504`
  ```php
  protected function blank_hidden_total_and_adjust_bounds($courseid, $course_item, $finalgrade) {
  ```
- `public/grade/report/lib.php:641`
  ```php
  protected function blank_hidden_total($courseid, $course_item, $finalgrade) {
  ```
- `public/grade/report/lib.php:658`
  ```php
  public static function calculate_average(grade_item $gradeitem, array $info): array {
  ```
- `public/grade/report/lib.php:700`
  ```php
  public function show_only_active(): bool {
  ```
- `public/grade/report/lib.php:718`
  ```php
  public function ungraded_counts(bool $grouponly = false, bool $includehiddengrades = false, $showonlyactiveenrol = true): array {
  ```
- `public/grade/report/lib.php:819`
  ```php
  public function item_types(): array {
  ```
- `public/grade/report/lib.php:852`
  ```php
  public static function get_gradable_users(int $courseid, ?int $groupid = null): array {
  ```
- `public/grade/report/lib.php:870`
  ```php
  protected function format_averages(array $ungradedcounts): html_table_row {
  ```
- `public/grade/report/lib.php:924`
  ```php
  protected function format_average_cell(grade_item $gradeitem, ?array $aggr = null, ?bool $shownumberofgrades = null): html_table_cell {
  ```
- `public/grade/report/outcomes/classes/event/grade_report_viewed.php:44`
  ```php
  public static function get_name() {
  ```
- `public/grade/report/outcomes/classes/privacy/provider.php:43`
  ```php
  public static function get_reason(): string {
  ```
- `public/grade/report/overview/classes/event/grade_report_viewed.php:43`
  ```php
  protected function init() {
  ```
- `public/grade/report/overview/classes/event/grade_report_viewed.php:53`
  ```php
  public static function get_name() {
  ```
- `public/grade/report/overview/classes/event/grade_report_viewed.php:62`
  ```php
  protected function validate_data() {
  ```
- `public/grade/report/overview/classes/external.php:135`
  ```php
  public static function get_course_grades_returns() {
  ```
- `public/grade/report/overview/classes/external.php:159`
  ```php
  public static function view_grade_report_parameters() {
  ```
- `public/grade/report/overview/classes/external.php:177`
  ```php
  public static function view_grade_report($courseid, $userid = 0) {
  ```
- `public/grade/report/overview/classes/external.php:223`
  ```php
  public static function view_grade_report_returns() {
  ```
- `public/grade/report/overview/classes/external.php:54`
  ```php
  public static function get_course_grades_parameters() {
  ```
- `public/grade/report/overview/classes/external.php:70`
  ```php
  public static function get_course_grades($userid = 0) {
  ```
- `public/grade/report/overview/classes/privacy/provider.php:43`
  ```php
  public static function get_reason(): string {
  ```
- `public/grade/report/overview/db/upgrade.php:31`
  ```php
  function xmldb_gradereport_overview_upgrade($oldversion) {
  ```
- `public/grade/report/overview/lib.php:135`
  ```php
  public function regrade_all_courses_if_needed(bool $frontend = false): void {
  ```
- `public/grade/report/overview/lib.php:148`
  ```php
  public function setup_table() {
  ```
- `public/grade/report/overview/lib.php:184`
  ```php
  public function setup_courses_data($studentcoursesonly) {
  ```
- `public/grade/report/overview/lib.php:275`
  ```php
  public function fill_table($activitylink = false, $studentcoursesonly = false) {
  ```
- `public/grade/report/overview/lib.php:347`
  ```php
  public function print_table($return=false) {
  ```
- `public/grade/report/overview/lib.php:361`
  ```php
  public function print_teacher_table() {
  ```
- `public/grade/report/overview/lib.php:379`
  ```php
  function process_data($data) {
  ```
- `public/grade/report/overview/lib.php:381`
  ```php
  function process_action($target, $action) {
  ```
- `public/grade/report/overview/lib.php:387`
  ```php
  public static function supports_mygrades() {
  ```
- `public/grade/report/overview/lib.php:402`
  ```php
  public static function check_access($systemcontext, $context, $personalcontext, $course, $userid) {
  ```
- `public/grade/report/overview/lib.php:437`
  ```php
  public static function viewed($context, $courseid, $userid) {
  ```
- `public/grade/report/overview/lib.php:449`
  ```php
  function grade_report_overview_settings_definition(&$mform) {
  ```
- `public/grade/report/overview/lib.php:490`
  ```php
  function gradereport_overview_myprofile_navigation(core_user\output\myprofile\tree $tree, $user, $iscurrentuser, $course) {
  ```
- `public/grade/report/overview/lib.php:71`
  ```php
  public function __construct($userid, $gpr, $context) {
  ```
- `public/grade/report/overview/renderer.php:36`
  ```php
  public function graded_users_selector($report, $course, $userid, $groupid, $includeall) {
  ```
- `public/grade/report/overview/tests/externallib_test.php:103`
  ```php
  public function test_get_course_grades_student(): void {
  ```
- `public/grade/report/overview/tests/externallib_test.php:147`
  ```php
  public function test_get_course_grades_admin(): void {
  ```
- `public/grade/report/overview/tests/externallib_test.php:181`
  ```php
  public function test_get_course_grades_teacher(): void {
  ```
- `public/grade/report/overview/tests/externallib_test.php:194`
  ```php
  public function test_get_course_grades_permissions(): void {
  ```
- `public/grade/report/overview/tests/externallib_test.php:205`
  ```php
  public function test_view_grade_report(): void {
  ```
- `public/grade/report/overview/tests/externallib_test.php:239`
  ```php
  public function test_view_grade_report_permissions(): void {
  ```
- `public/grade/report/overview/tests/externallib_test.php:58`
  ```php
  public function setUp(): void {
  ```
- `public/grade/report/overview/tests/lib_test.php:32`
  ```php
  protected function setUp(): void {
  ```
- `public/grade/report/overview/tests/lib_test.php:44`
  ```php
  public static function true_or_false(): array {
  ```
- `public/grade/report/overview/tests/lib_test.php:62`
  ```php
  public function test_regrade_all_courses_if_needed(bool $frontend): void {
  ```
- `public/grade/report/singleview/classes/event/grade_report_viewed.php:44`
  ```php
  public static function get_name() {
  ```
- `public/grade/report/singleview/classes/event/grade_report_viewed.php:53`
  ```php
  protected function validate_data() {
  ```
- `public/grade/report/singleview/classes/external/singleview.php:100`
  ```php
  public static function get_grade_items_for_search_widget_returns(): external_single_structure {
  ```
- `public/grade/report/singleview/classes/external/singleview.php:46`
  ```php
  public static function get_grade_items_for_search_widget_parameters(): external_function_parameters {
  ```
- `public/grade/report/singleview/classes/external/singleview.php:64`
  ```php
  protected static function get_grade_items_for_search_widget(int $courseid): array {
  ```
- `public/grade/report/singleview/classes/local/screen/filterable_items.php:43`
  ```php
  public static function filter($item): bool;
  ```
- `public/grade/report/singleview/classes/local/screen/grade.php:102`
  ```php
  public function select_label(): string {
  ```
- `public/grade/report/singleview/classes/local/screen/grade.php:110`
  ```php
  public function description(): string {
  ```
- `public/grade/report/singleview/classes/local/screen/grade.php:119`
  ```php
  public function options(): array {
  ```
- `public/grade/report/singleview/classes/local/screen/grade.php:132`
  ```php
  public function item_type(): string {
  ```
- `public/grade/report/singleview/classes/local/screen/grade.php:140`
  ```php
  public function original_definition(): array {
  ```
- `public/grade/report/singleview/classes/local/screen/grade.php:154`
  ```php
  public function init($selfitemisempty = false) {
  ```
- `public/grade/report/singleview/classes/local/screen/grade.php:197`
  ```php
  public function original_headers() {
  ```
- `public/grade/report/singleview/classes/local/screen/grade.php:215`
  ```php
  public function format_line($item): array {
  ```
- `public/grade/report/singleview/classes/local/screen/grade.php:287`
  ```php
  public function item_range() {
  ```
- `public/grade/report/singleview/classes/local/screen/grade.php:300`
  ```php
  public function supports_paging(): bool {
  ```
- `public/grade/report/singleview/classes/local/screen/grade.php:309`
  ```php
  public function pager(): string {
  ```
- `public/grade/report/singleview/classes/local/screen/grade.php:329`
  ```php
  public function heading(): string {
  ```
- `public/grade/report/singleview/classes/local/screen/grade.php:340`
  ```php
  public function summary(): string {
  ```
- `public/grade/report/singleview/classes/local/screen/grade.php:350`
  ```php
  public function process($data): \stdClass {
  ```
- `public/grade/report/singleview/classes/local/screen/grade.php:418`
  ```php
  private function get_user_action_menu(\stdClass $user) {
  ```
- `public/grade/report/singleview/classes/local/screen/grade.php:84`
  ```php
  public static function allowcategories(): bool {
  ```
- `public/grade/report/singleview/classes/local/screen/grade.php:93`
  ```php
  public static function filter($item): bool {
  ```
- `public/grade/report/singleview/classes/local/screen/grade_select.php:27`
  ```php
  public function init($selfitemisempty = false) {
  ```
- `public/grade/report/singleview/classes/local/screen/grade_select.php:34`
  ```php
  public function html(): string {
  ```
- `public/grade/report/singleview/classes/local/screen/grade_select.php:56`
  ```php
  public function item_type(): ?string {
  ```
- `public/grade/report/singleview/classes/local/screen/grade_select.php:64`
  ```php
  public function display_group_selector(): bool {
  ```
- `public/grade/report/singleview/classes/local/screen/grade_select.php:73`
  ```php
  public function heading(): string {
  ```
- `public/grade/report/singleview/classes/local/screen/grade_select.php:82`
  ```php
  public function supports_paging(): bool {
  ```
- `public/grade/report/singleview/classes/local/screen/screen.php:118`
  ```php
  public function __construct(int $courseid, ?int $itemid, ?int $groupid = null) {
  ```
- `public/grade/report/singleview/classes/local/screen/screen.php:153`
  ```php
  public function setup_structure() {
  ```
- `public/grade/report/singleview/classes/local/screen/screen.php:166`
  ```php
  public function format_link(string $screen, int $itemid, ?bool $display = null): string {
  ```
- `public/grade/report/singleview/classes/local/screen/screen.php:188`
  ```php
  public function fetch_grade_or_default(grade_item $item, int $userid): grade_grade {
  ```
- `public/grade/report/singleview/classes/local/screen/screen.php:213`
  ```php
  public function heading(): string {
  ```
- `public/grade/report/singleview/classes/local/screen/screen.php:243`
  ```php
  public function supports_paging(): bool {
  ```
- `public/grade/report/singleview/classes/local/screen/screen.php:252`
  ```php
  public function pager(): string {
  ```
- `public/grade/report/singleview/classes/local/screen/screen.php:259`
  ```php
  public function js() {
  ```
- `public/grade/report/singleview/classes/local/screen/screen.php:273`
  ```php
  public function process($data): stdClass {
  ```
- `public/grade/report/singleview/classes/local/screen/screen.php:387`
  ```php
  public function options(): array {
  ```
- `public/grade/report/singleview/classes/local/screen/screen.php:395`
  ```php
  public function display_group_selector(): bool {
  ```
- `public/grade/report/singleview/classes/local/screen/screen.php:403`
  ```php
  public function supports_next_prev(): bool {
  ```
- `public/grade/report/singleview/classes/local/screen/screen.php:413`
  ```php
  protected function load_users(): array {
  ```
- `public/grade/report/singleview/classes/local/screen/screen.php:424`
  ```php
  public function perpage_select(): string {
  ```
- `public/grade/report/singleview/classes/local/screen/selectable_items.php:41`
  ```php
  public function description(): string;
  ```
- `public/grade/report/singleview/classes/local/screen/selectable_items.php:47`
  ```php
  public function select_label(): string;
  ```
- `public/grade/report/singleview/classes/local/screen/selectable_items.php:53`
  ```php
  public function options(): array;
  ```
- `public/grade/report/singleview/classes/local/screen/selectable_items.php:59`
  ```php
  public function item_type(): string;
  ```
- `public/grade/report/singleview/classes/local/screen/tablelike.php:101`
  ```php
  public function headers(): array {
  ```
- `public/grade/report/singleview/classes/local/screen/tablelike.php:111`
  ```php
  public function set_headers(array $overwrite): tablelike {
  ```
- `public/grade/report/singleview/classes/local/screen/tablelike.php:121`
  ```php
  public function init_errors(): array {
  ```
- `public/grade/report/singleview/classes/local/screen/tablelike.php:130`
  ```php
  public function set_init_error(string $mesg) {
  ```
- `public/grade/report/singleview/classes/local/screen/tablelike.php:139`
  ```php
  public function definition(): array {
  ```
- `public/grade/report/singleview/classes/local/screen/tablelike.php:149`
  ```php
  public function set_definition(array $overwrite): tablelike {
  ```
- `public/grade/report/singleview/classes/local/screen/tablelike.php:159`
  ```php
  public function format_definition(grade_grade $grade): array {
  ```
- `public/grade/report/singleview/classes/local/screen/tablelike.php:186`
  ```php
  public function html(): string {
  ```
- `public/grade/report/singleview/classes/local/screen/tablelike.php:233`
  ```php
  public function bulk_insert() {
  ```
- `public/grade/report/singleview/classes/local/screen/tablelike.php:246`
  ```php
  public function is_readonly(): bool {
  ```
- `public/grade/report/singleview/classes/local/screen/tablelike.php:257`
  ```php
  public function buttons(bool $disabled = false): array {
  ```
- `public/grade/report/singleview/classes/local/screen/user.php:103`
  ```php
  public function init($selfitemisempty = false) {
  ```
- `public/grade/report/singleview/classes/local/screen/user.php:150`
  ```php
  public function original_headers(): array {
  ```
- `public/grade/report/singleview/classes/local/screen/user.php:169`
  ```php
  public function format_line($item): array {
  ```
- `public/grade/report/singleview/classes/local/screen/user.php:252`
  ```php
  private function format_icon($item): string {
  ```
- `public/grade/report/singleview/classes/local/screen/user.php:263`
  ```php
  private function get_item_action_menu(grade_item $item) {
  ```
- `public/grade/report/singleview/classes/local/screen/user.php:288`
  ```php
  private function category(grade_item $item): string {
  ```
- `public/grade/report/singleview/classes/local/screen/user.php:317`
  ```php
  public function heading(): string {
  ```
- `public/grade/report/singleview/classes/local/screen/user.php:328`
  ```php
  public function summary(): string {
  ```
- `public/grade/report/singleview/classes/local/screen/user.php:337`
  ```php
  public function pager(): string {
  ```
- `public/grade/report/singleview/classes/local/screen/user.php:361`
  ```php
  public function supports_paging(): bool {
  ```
- `public/grade/report/singleview/classes/local/screen/user.php:372`
  ```php
  public function process($data): stdClass {
  ```
- `public/grade/report/singleview/classes/local/screen/user.php:63`
  ```php
  public function select_label(): string {
  ```
- `public/grade/report/singleview/classes/local/screen/user.php:72`
  ```php
  public function description(): string {
  ```
- `public/grade/report/singleview/classes/local/screen/user.php:81`
  ```php
  public function options(): array {
  ```
- `public/grade/report/singleview/classes/local/screen/user.php:94`
  ```php
  public function item_type(): string {
  ```
- `public/grade/report/singleview/classes/local/screen/user_select.php:29`
  ```php
  public function init($selfitemisempty = false) {
  ```
- `public/grade/report/singleview/classes/local/screen/user_select.php:36`
  ```php
  public function html(): string {
  ```
- `public/grade/report/singleview/classes/local/screen/user_select.php:58`
  ```php
  public function item_type(): ?string {
  ```
- `public/grade/report/singleview/classes/local/screen/user_select.php:66`
  ```php
  public function display_group_selector(): bool {
  ```
- `public/grade/report/singleview/classes/local/screen/user_select.php:75`
  ```php
  public function heading(): string {
  ```
- `public/grade/report/singleview/classes/local/screen/user_select.php:84`
  ```php
  public function supports_paging(): bool {
  ```
- `public/grade/report/singleview/classes/local/ui/attribute_format.php:48`
  ```php
  public function __toString(): string {
  ```
- `public/grade/report/singleview/classes/local/ui/be_checked.php:41`
  ```php
  public function is_checked(): bool;
  ```
- `public/grade/report/singleview/classes/local/ui/be_disabled.php:39`
  ```php
  public function is_disabled(): bool;
  ```
- `public/grade/report/singleview/classes/local/ui/be_readonly.php:37`
  ```php
  public function is_readonly(): bool;
  ```
- `public/grade/report/singleview/classes/local/ui/bulk_insert.php:122`
  ```php
  private function name_for($extend) {
  ```
- `public/grade/report/singleview/classes/local/ui/bulk_insert.php:52`
  ```php
  public function __construct($item) {
  ```
- `public/grade/report/singleview/classes/local/ui/bulk_insert.php:65`
  ```php
  public function is_applied($data): bool {
  ```
- `public/grade/report/singleview/classes/local/ui/bulk_insert.php:75`
  ```php
  public function get_type($data): string {
  ```
- `public/grade/report/singleview/classes/local/ui/bulk_insert.php:85`
  ```php
  public function get_insert_value($data): string {
  ```
- `public/grade/report/singleview/classes/local/ui/bulk_insert.php:94`
  ```php
  public function html(): string {
  ```
- `public/grade/report/singleview/classes/local/ui/checkbox_attribute.php:57`
  ```php
  public function __construct(string $name, string $label, bool $ischecked = false, int $locked=0, bool $isreadonly = false) {
  ```
- `public/grade/report/singleview/classes/local/ui/checkbox_attribute.php:68`
  ```php
  public function is_checkbox(): bool {
  ```
- `public/grade/report/singleview/classes/local/ui/checkbox_attribute.php:77`
  ```php
  public function html(): string {
  ```
- `public/grade/report/singleview/classes/local/ui/dropdown_attribute.php:69`
  ```php
  public function __construct(
  ```
- `public/grade/report/singleview/classes/local/ui/dropdown_attribute.php:89`
  ```php
  public function is_dropdown(): bool {
  ```
- `public/grade/report/singleview/classes/local/ui/dropdown_attribute.php:98`
  ```php
  public function html(): string {
  ```
- `public/grade/report/singleview/classes/local/ui/element.php:63`
  ```php
  public function __construct(string $name, string $value, string $label) {
  ```
- `public/grade/report/singleview/classes/local/ui/element.php:73`
  ```php
  public function is_checkbox(): bool {
  ```
- `public/grade/report/singleview/classes/local/ui/element.php:81`
  ```php
  public function is_textbox(): bool {
  ```
- `public/grade/report/singleview/classes/local/ui/element.php:89`
  ```php
  public function is_dropdown(): bool {
  ```
- `public/grade/report/singleview/classes/local/ui/empty_element.php:46`
  ```php
  public function __construct(?string $msg = null) {
  ```
- `public/grade/report/singleview/classes/local/ui/empty_element.php:59`
  ```php
  public function html(): string {
  ```
- `public/grade/report/singleview/classes/local/ui/exclude.php:105`
  ```php
  public function get_label(): string {
  ```
- `public/grade/report/singleview/classes/local/ui/exclude.php:118`
  ```php
  public function set($value) {
  ```
- `public/grade/report/singleview/classes/local/ui/exclude.php:58`
  ```php
  public function is_checked(): bool {
  ```
- `public/grade/report/singleview/classes/local/ui/exclude.php:67`
  ```php
  public function is_disabled(): bool {
  ```
- `public/grade/report/singleview/classes/local/ui/exclude.php:76`
  ```php
  public function is_readonly(): bool {
  ```
- `public/grade/report/singleview/classes/local/ui/exclude.php:86`
  ```php
  public function determine_format(): element {
  ```
- `public/grade/report/singleview/classes/local/ui/feedback.php:108`
  ```php
  public function determine_format(): element {
  ```
- `public/grade/report/singleview/classes/local/ui/feedback.php:128`
  ```php
  public function set($value) {
  ```
- `public/grade/report/singleview/classes/local/ui/feedback.php:51`
  ```php
  public function get_value(): ?string {
  ```
- `public/grade/report/singleview/classes/local/ui/feedback.php:60`
  ```php
  public function get_label(): string {
  ```
- `public/grade/report/singleview/classes/local/ui/feedback.php:72`
  ```php
  public function is_disabled(): bool {
  ```
- `public/grade/report/singleview/classes/local/ui/feedback.php:98`
  ```php
  public function is_readonly(): bool {
  ```
- `public/grade/report/singleview/classes/local/ui/finalgrade.php:106`
  ```php
  public function is_readonly(): bool {
  ```
- `public/grade/report/singleview/classes/local/ui/finalgrade.php:116`
  ```php
  public function determine_format(): element {
  ```
- `public/grade/report/singleview/classes/local/ui/finalgrade.php:179`
  ```php
  public function set($value) {
  ```
- `public/grade/report/singleview/classes/local/ui/finalgrade.php:51`
  ```php
  public function get_value(): ?string {
  ```
- `public/grade/report/singleview/classes/local/ui/finalgrade.php:67`
  ```php
  public function get_label(): string {
  ```
- `public/grade/report/singleview/classes/local/ui/finalgrade.php:79`
  ```php
  public function is_disabled(): bool {
  ```
- `public/grade/report/singleview/classes/local/ui/grade_attribute_format.php:63`
  ```php
  public function __construct($grade = 0) {
  ```
- `public/grade/report/singleview/classes/local/ui/grade_attribute_format.php:72`
  ```php
  public function get_name(): string {
  ```
- `public/grade/report/singleview/classes/local/ui/override.php:121`
  ```php
  public function set($value) {
  ```
- `public/grade/report/singleview/classes/local/ui/override.php:51`
  ```php
  public function is_checked(): bool {
  ```
- `public/grade/report/singleview/classes/local/ui/override.php:60`
  ```php
  public function is_disabled(): bool {
  ```
- `public/grade/report/singleview/classes/local/ui/override.php:76`
  ```php
  public function is_readonly(): bool {
  ```
- `public/grade/report/singleview/classes/local/ui/override.php:86`
  ```php
  public function get_label(): string {
  ```
- `public/grade/report/singleview/classes/local/ui/override.php:98`
  ```php
  public function determine_format(): element {
  ```
- `public/grade/report/singleview/classes/local/ui/range.php:47`
  ```php
  public function __construct(grade_item $item) {
  ```
- `public/grade/report/singleview/classes/local/ui/range.php:56`
  ```php
  public function determine_format(): element {
  ```
- `public/grade/report/singleview/classes/local/ui/text_attribute.php:129`
  ```php
  public function set_type(?string $type): void {
  ```
- `public/grade/report/singleview/classes/local/ui/text_attribute.php:139`
  ```php
  public function set_min(?string $min): void {
  ```
- `public/grade/report/singleview/classes/local/ui/text_attribute.php:149`
  ```php
  public function set_max(?string $max): void {
  ```
- `public/grade/report/singleview/classes/local/ui/text_attribute.php:74`
  ```php
  public function __construct(string $name, string $value, string $label, bool $isdisabled = false, bool $isreadonly = false) {
  ```
- `public/grade/report/singleview/classes/local/ui/text_attribute.php:84`
  ```php
  public function is_textbox(): bool {
  ```
- `public/grade/report/singleview/classes/local/ui/text_attribute.php:92`
  ```php
  public function html(): string {
  ```
- `public/grade/report/singleview/classes/local/ui/unique_name.php:42`
  ```php
  public function get_name(): string;
  ```
- `public/grade/report/singleview/classes/local/ui/unique_value.php:41`
  ```php
  public function get_value(): ?string;
  ```
- `public/grade/report/singleview/classes/output/action_bar.php:47`
  ```php
  public function __construct(\context $context, singleview $report, string $itemtype) {
  ```
- `public/grade/report/singleview/classes/output/action_bar.php:58`
  ```php
  public function get_template(): string {
  ```
- `public/grade/report/singleview/classes/output/action_bar.php:68`
  ```php
  public function export_for_template(renderer_base $output) {
  ```
- `public/grade/report/singleview/classes/privacy/provider.php:43`
  ```php
  public static function get_reason(): string {
  ```
- `public/grade/report/singleview/classes/report/singleview.php:127`
  ```php
  public function output(): string {
  ```
- `public/grade/report/singleview/classes/report/singleview.php:132`
  ```php
  protected function setup_groups() {
  ```
- `public/grade/report/singleview/classes/report/singleview.php:146`
  ```php
  protected static function groups_course_menu(stdClass $course) {
  ```
- `public/grade/report/singleview/classes/report/singleview.php:158`
  ```php
  protected function setup_item_selector(string $itemtype, ?int $itemid) {
  ```
- `public/grade/report/singleview/classes/report/singleview.php:176`
  ```php
  public function bulk_actions_menu(renderer_base $output): string {
  ```
- `public/grade/report/singleview/classes/report/singleview.php:49`
  ```php
  public static function valid_screens(): array {
  ```
- `public/grade/report/singleview/classes/report/singleview.php:60`
  ```php
  public function process_data($data) {
  ```
- `public/grade/report/singleview/classes/report/singleview.php:72`
  ```php
  public function process_action($target, $action) {
  ```
- `public/grade/report/singleview/classes/report/singleview.php:85`
  ```php
  public function __construct(
  ```
- `public/grade/report/singleview/lib.php:36`
  ```php
  function gradereport_singleview_get_report_link(context_course $context, int $courseid,
  ```
- `public/grade/report/singleview/renderer.php:130`
  ```php
  public function report_navigation(object $gpr, int $courseid, \context_course $context, singleview $report,
  ```
- `public/grade/report/singleview/renderer.php:47`
  ```php
  public function users_selector(object $course, ?int $userid = null, ?int $groupid = null): string {
  ```
- `public/grade/report/singleview/renderer.php:79`
  ```php
  public function grade_items_selector(object $course, ?int $gradeitemid = null): string {
  ```
- `public/grade/report/singleview/tests/fixtures/screen.php:34`
  ```php
  public function test_load_users(): array {
  ```
- `public/grade/report/singleview/tests/fixtures/screen.php:41`
  ```php
  public function init($selfitemisempty = false) {
  ```
- `public/grade/report/singleview/tests/fixtures/screen.php:47`
  ```php
  public function item_type(): string {
  ```
- `public/grade/report/singleview/tests/fixtures/screen.php:53`
  ```php
  public function html(): string {
  ```
- `public/grade/report/singleview/tests/screen_test.php:40`
  ```php
  public function test_load_users(): void {
  ```
- `public/grade/report/summary/classes/local/entities/grade_items.php:109`
  ```php
  protected function get_available_columns(): array {
  ```
- `public/grade/report/summary/classes/local/entities/grade_items.php:214`
  ```php
  protected function get_available_filters(): array {
  ```
- `public/grade/report/summary/classes/local/entities/grade_items.php:57`
  ```php
  public function __construct(stdClass $course) {
  ```
- `public/grade/report/summary/classes/local/entities/grade_items.php:66`
  ```php
  protected function get_default_tables(): array {
  ```
- `public/grade/report/summary/classes/local/entities/grade_items.php:77`
  ```php
  protected function get_default_entity_title(): lang_string {
  ```
- `public/grade/report/summary/classes/local/entities/grade_items.php:86`
  ```php
  public function initialise(): base {
  ```
- `public/grade/report/summary/classes/local/systemreports/summary.php:104`
  ```php
  protected function add_filters(): void {
  ```
- `public/grade/report/summary/classes/local/systemreports/summary.php:35`
  ```php
  protected function initialise(): void {
  ```
- `public/grade/report/summary/classes/local/systemreports/summary.php:78`
  ```php
  protected function can_view(): bool {
  ```
- `public/grade/report/summary/classes/local/systemreports/summary.php:88`
  ```php
  public function add_columns(): void {
  ```
- `public/grade/report/summary/classes/privacy/provider.php:41`
  ```php
  public static function get_reason(): string {
  ```
- `public/grade/report/summary/lib.php:53`
  ```php
  public function __construct($courseid, $gpr, $context) {
  ```
- `public/grade/report/summary/lib.php:66`
  ```php
  public function process_action($target, $action) {
  ```
- `public/grade/report/summary/lib.php:74`
  ```php
  public function process_data($data) {
  ```
- `public/grade/report/summary/lib.php:82`
  ```php
  public function show_only_active(): bool {
  ```
- `public/grade/report/user/classes/event/grade_report_viewed.php:43`
  ```php
  protected function init(): void {
  ```
- `public/grade/report/user/classes/event/grade_report_viewed.php:53`
  ```php
  public static function get_name(): string {
  ```
- `public/grade/report/user/classes/event/grade_report_viewed.php:62`
  ```php
  protected function validate_data() {
  ```
- `public/grade/report/user/classes/external/get_access_information.php:44`
  ```php
  public static function execute_parameters(): external_function_parameters {
  ```
- `public/grade/report/user/classes/external/get_access_information.php:59`
  ```php
  public static function execute(int $courseid): array {
  ```
- `public/grade/report/user/classes/external/get_access_information.php:81`
  ```php
  public static function execute_returns(): external_single_structure {
  ```
- `public/grade/report/user/classes/external/user.php:146`
  ```php
  protected static function get_report_data(
  ```
- `public/grade/report/user/classes/external/user.php:237`
  ```php
  public static function get_grades_table_parameters(): external_function_parameters {
  ```
- `public/grade/report/user/classes/external/user.php:257`
  ```php
  public static function get_grades_table(int $courseid, int $userid = 0, int $groupid = 0): array {
  ```
- `public/grade/report/user/classes/external/user.php:277`
  ```php
  private static function grades_table_column(): array {
  ```
- `public/grade/report/user/classes/external/user.php:291`
  ```php
  public static function get_grades_table_returns(): external_single_structure {
  ```
- `public/grade/report/user/classes/external/user.php:365`
  ```php
  public static function view_grade_report_parameters(): external_function_parameters {
  ```
- `public/grade/report/user/classes/external/user.php:383`
  ```php
  public static function view_grade_report(int $courseid, int $userid = 0): array {
  ```
- `public/grade/report/user/classes/external/user.php:439`
  ```php
  public static function view_grade_report_returns(): external_description {
  ```
- `public/grade/report/user/classes/external/user.php:454`
  ```php
  public static function get_grade_items_parameters(): external_function_parameters {
  ```
- `public/grade/report/user/classes/external/user.php:468`
  ```php
  public static function get_grade_items(int $courseid, int $userid = 0, int $groupid = 0): array {
  ```
- `public/grade/report/user/classes/external/user.php:495`
  ```php
  public static function get_grade_items_returns(): external_single_structure {
  ```
- `public/grade/report/user/classes/external/user.php:56`
  ```php
  protected static function check_report_access(int $courseid, int $userid, int $groupid = 0): array {
  ```
- `public/grade/report/user/classes/output/action_bar.php:52`
  ```php
  public function __construct(
  ```
- `public/grade/report/user/classes/output/action_bar.php:71`
  ```php
  public function get_template(): string {
  ```
- `public/grade/report/user/classes/output/action_bar.php:81`
  ```php
  public function export_for_template(\renderer_base $output): array {
  ```
- `public/grade/report/user/classes/privacy/provider.php:52`
  ```php
  public static function get_metadata(collection $items): collection {
  ```
- `public/grade/report/user/classes/privacy/provider.php:64`
  ```php
  public static function export_user_preferences(int $userid) {
  ```
- `public/grade/report/user/classes/report/user.php:1070`
  ```php
  public function print_table(bool $return = false) {
  ```
- `public/grade/report/user/classes/report/user.php:1161`
  ```php
  public function process_data($data): void {
  ```
- `public/grade/report/user/classes/report/user.php:1171`
  ```php
  public function process_action($target, $action): void {
  ```
- `public/grade/report/user/classes/report/user.php:1178`
  ```php
  public function output_report_zerostate(): string {
  ```
- `public/grade/report/user/classes/report/user.php:1192`
  ```php
  public function viewed() {
  ```
- `public/grade/report/user/classes/report/user.php:228`
  ```php
  public function __construct(int $courseid, ?object $gpr, object $context, int $userid, ?bool $viewasuser = null) {
  ```
- `public/grade/report/user/classes/report/user.php:357`
  ```php
  protected function format_average_cell(grade_item $gradeitem, ?array $aggr = null, ?bool $shownumberofgrades = null): \html_table_cell {
  ```
- `public/grade/report/user/classes/report/user.php:382`
  ```php
  public function inject_rowspans(array &$element): int {
  ```
- `public/grade/report/user/classes/report/user.php:416`
  ```php
  public function setup_table() {
  ```
- `public/grade/report/user/classes/report/user.php:478`
  ```php
  public function fill_table(): bool {
  ```
- `public/grade/report/user/classes/report/user.php:488`
  ```php
  private function fill_table_recursive(array &$element) {
  ```
- `public/grade/report/user/classes/report/user.php:979`
  ```php
  public function fill_contributions_column(array $element) {
  ```
- `public/grade/report/user/db/upgrade.php:29`
  ```php
  function xmldb_gradereport_user_upgrade($oldversion) {
  ```
- `public/grade/report/user/lib.php:176`
  ```php
  function grade_report_user_profilereport(object $course, object $user, bool $viewasuser = false) {
  ```
- `public/grade/report/user/lib.php:217`
  ```php
  function gradereport_user_myprofile_navigation(tree $tree, stdClass $user, bool $iscurrentuser, ?stdClass $course) {
  ```
- `public/grade/report/user/lib.php:264`
  ```php
  function gradereport_user_get_report_link(context_course $context, int $courseid, array $element,
  ```
- `public/grade/report/user/lib.php:37`
  ```php
  function grade_report_user_settings_definition(&$mform) {
  ```
- `public/grade/report/user/renderer.php:123`
  ```php
  public function user_navigation(graded_users_iterator $gui, int $userid, int $courseid): string {
  ```
- `public/grade/report/user/renderer.php:177`
  ```php
  public function view_mode_selector(int $userid, int $userview, int $courseid): string {
  ```
- `public/grade/report/user/renderer.php:49`
  ```php
  public function graded_users_selector(string $report, stdClass $course, int $userid, ?int $groupid, bool $includeall): string {
  ```
- `public/grade/report/user/renderer.php:65`
  ```php
  public function view_user_selector(int $userid, int $userview): string {
  ```
- `public/grade/report/user/renderer.php:96`
  ```php
  public function users_selector(object $course, ?int $userid = null, ?int $groupid = null, string $usersearch = ''): string {
  ```
- `public/grade/report/user/tests/externallib_test.php:124`
  ```php
  public function test_get_grades_table_student(): void {
  ```
- `public/grade/report/user/tests/externallib_test.php:168`
  ```php
  public function test_get_grades_table_permissions(): void {
  ```
- `public/grade/report/user/tests/externallib_test.php:192`
  ```php
  public function test_view_grade_report(): void {
  ```
- `public/grade/report/user/tests/externallib_test.php:241`
  ```php
  public function test_get_grade_items_teacher(): void {
  ```
- `public/grade/report/user/tests/externallib_test.php:345`
  ```php
  public function test_get_grade_items_student(): void {
  ```
- `public/grade/report/user/tests/externallib_test.php:37`
  ```php
  private function load_data(int $s1grade, int $s2grade, int $s3grade): array {
  ```
- `public/grade/report/user/tests/externallib_test.php:90`
  ```php
  public function test_get_grades_table_teacher(): void {
  ```
- `public/grade/report/user/tests/lib_test.php:55`
  ```php
  public function setUp(): void {
  ```
- `public/grade/report/user/tests/lib_test.php:66`
  ```php
  public function test_gradereport_user_myprofile_navigation(): void {
  ```
- `public/grade/report/user/tests/lib_test.php:80`
  ```php
  public function test_gradereport_user_myprofile_navigation_without_permission(): void {
  ```
- `public/grade/report/user/tests/privacy/provider_test.php:42`
  ```php
  public function setUp(): void {
  ```
- `public/grade/report/user/tests/privacy/provider_test.php:50`
  ```php
  public function test_export_user_preferences_not_defined(): void {
  ```
- `public/grade/report/user/tests/privacy/provider_test.php:62`
  ```php
  public function test_export_user_preferences_single(): void {
  ```
- `public/grade/tests/behat/behat_grade.php:103`
  ```php
  public function i_hide_the_grade_item(string $gradeitem, string $type, string $page) {
  ```
- `public/grade/tests/behat/behat_grade.php:122`
  ```php
  public function i_duplicate_the_grade_item(string $gradeitem) {
  ```
- `public/grade/tests/behat/behat_grade.php:141`
  ```php
  public function i_set_calculation_for_grade_item_with_idnumbers($calculation, $gradeitem, TableNode $data) {
  ```
- `public/grade/tests/behat/behat_grade.php:181`
  ```php
  public function i_set_calculation_for_grade_category_with_idnumbers(string $calculation, string $gradeitem, TableNode $data) {
  ```
- `public/grade/tests/behat/behat_grade.php:220`
  ```php
  public function i_reset_weights_for_grade_category(string $gradeitem) {
  ```
- `public/grade/tests/behat/behat_grade.php:236`
  ```php
  public function gradebook_calculations_for_the_course_are_frozen_at_version($coursename, $version) {
  ```
- `public/grade/tests/behat/behat_grade.php:257`
  ```php
  public function i_navigate_to_in_the_course_gradebook($gradepath) {
  ```
- `public/grade/tests/behat/behat_grade.php:277`
  ```php
  public function i_navigate_to_import_page_in_the_course_gradebook($gradeimportoption) {
  ```
- `public/grade/tests/behat/behat_grade.php:292`
  ```php
  public function i_navigate_to_export_page_in_the_course_gradebook($gradeexportoption) {
  ```
- `public/grade/tests/behat/behat_grade.php:42`
  ```php
  public function i_give_the_grade($grade, $userfullname, $itemname) {
  ```
- `public/grade/tests/behat/behat_grade.php:61`
  ```php
  public function i_set_the_following_settings_for_grade_item(string $gradeitem, string $type, string $page, TableNode $data) {
  ```
- `public/grade/tests/behat/behat_grade_deprecated.php:128`
  ```php
  public function i_click_on_in_search_widget(string $needle, string $haystack) {
  ```
- `public/grade/tests/behat/behat_grade_deprecated.php:48`
  ```php
  public function i_confirm_in_search_within_the_gradebook_widget_exists($needle, $haystack) {
  ```
- `public/grade/tests/behat/behat_grade_deprecated.php:88`
  ```php
  public function i_confirm_in_search_within_the_gradebook_widget_does_not_exist($needle, $haystack) {
  ```
- `public/grade/tests/behat/behat_grades.php:136`
  ```php
  public function i_select_in_the($value, $element, $selectortype) {
  ```
- `public/grade/tests/behat/behat_grades.php:156`
  ```php
  protected function get_grade_id(int $itemid, int $userid): int {
  ```
- `public/grade/tests/behat/behat_grades.php:173`
  ```php
  protected function get_grade_item_id(string $itemname): int {
  ```
- `public/grade/tests/behat/behat_grades.php:206`
  ```php
  protected function get_course_grade_category_id(string $coursename): int {
  ```
- `public/grade/tests/behat/behat_grades.php:231`
  ```php
  protected function get_grade_category_id(string $categoryname): int {
  ```
- `public/grade/tests/behat/behat_grades.php:257`
  ```php
  public function i_click_on_grade_item_menu(string $itemname, string $itemtype, string $page) {
  ```
- `public/grade/tests/behat/behat_grades.php:299`
  ```php
  public function i_click_on_grade_menu(string $itemname, string $username) {
  ```
- `public/grade/tests/behat/behat_grades.php:35`
  ```php
  public static function get_partial_named_selectors(): array {
  ```
- `public/grade/tests/behat/behat_grades.php:67`
  ```php
  protected function resolve_page_instance_url(string $type, string $identifier): moodle_url {
  ```
- `public/grade/tests/component_gradeitem_test.php:100`
  ```php
  public function test_get_formatted_grade_for_user_with_scales(): void {
  ```
- `public/grade/tests/component_gradeitem_test.php:113`
  ```php
  public function test_get_formatted_grade_for_user_with_rubric(): void {
  ```
- `public/grade/tests/component_gradeitem_test.php:166`
  ```php
  public function test_get_formatted_grade_for_user_with_marking_guide(): void {
  ```
- `public/grade/tests/component_gradeitem_test.php:221`
  ```php
  protected function initialise_test_and_get_grade_item(int $gradeforum, int $gradegiven, ?int $displaytype = null): \stdClass {
  ```
- `public/grade/tests/component_gradeitem_test.php:254`
  ```php
  protected function get_forum_instance(array $config = []): forum_entity {
  ```
- `public/grade/tests/component_gradeitem_test.php:48`
  ```php
  public function test_get_formatted_grade_for_user_with_points(): void {
  ```
- `public/grade/tests/component_gradeitem_test.php:61`
  ```php
  public function test_get_formatted_grade_for_user_with_letters(): void {
  ```
- `public/grade/tests/component_gradeitem_test.php:74`
  ```php
  public function test_get_formatted_grade_for_user_with_percentage(): void {
  ```
- `public/grade/tests/component_gradeitem_test.php:87`
  ```php
  public function test_get_formatted_grade_for_user_with_points_letter(): void {
  ```
- `public/grade/tests/component_gradeitems_test.php:103`
  ```php
  public function test_is_valid_itemname(string $itemname, bool $isadvanced): void {
  ```
- `public/grade/tests/component_gradeitems_test.php:114`
  ```php
  public function test_defines_advancedgrading_itemnames_for_component_does_not_exist(): void {
  ```
- `public/grade/tests/component_gradeitems_test.php:121`
  ```php
  public function test_defines_advancedgrading_itemnames_for_component_no_interfaces(): void {
  ```
- `public/grade/tests/component_gradeitems_test.php:128`
  ```php
  public function test_defines_advancedgrading_itemnames_for_component_grading_no_interface(): void {
  ```
- `public/grade/tests/component_gradeitems_test.php:135`
  ```php
  public function test_defines_advancedgrading_itemnames_for_component_grading_has_interface(): void {
  ```
- `public/grade/tests/component_gradeitems_test.php:142`
  ```php
  public function test_get_advancedgrading_itemnames_for_component_does_not_exist(): void {
  ```
- `public/grade/tests/component_gradeitems_test.php:150`
  ```php
  public function test_get_advancedgrading_itemnames_for_component_no_interfaces(): void {
  ```
- `public/grade/tests/component_gradeitems_test.php:158`
  ```php
  public function test_get_advancedgrading_itemnames_for_component_grading_no_interface(): void {
  ```
- `public/grade/tests/component_gradeitems_test.php:166`
  ```php
  public function test_get_advancedgrading_itemnames_for_component(): void {
  ```
- `public/grade/tests/component_gradeitems_test.php:176`
  ```php
  public static function is_advancedgrading_itemname_provider(): array {
  ```
- `public/grade/tests/component_gradeitems_test.php:200`
  ```php
  public function test_is_advancedgrading_itemname(string $itemname, bool $isadvanced): void {
  ```
- `public/grade/tests/component_gradeitems_test.php:212`
  ```php
  public static function get_field_name_for_itemnumber_provider(): array {
  ```
- `public/grade/tests/component_gradeitems_test.php:245`
  ```php
  public function test_get_field_name_for_itemnumber(int $itemnumber, string $fieldname, string $expected): void {
  ```
- `public/grade/tests/component_gradeitems_test.php:253`
  ```php
  public function test_get_field_name_for_itemnumber_invalid_itemnumber(): void {
  ```
- `public/grade/tests/component_gradeitems_test.php:263`
  ```php
  public function test_get_field_name_for_itemnumber_component_not_defining_mapping_itemnumber_zero(): void {
  ```
- `public/grade/tests/component_gradeitems_test.php:272`
  ```php
  public function test_get_field_name_for_itemnumber_component_not_defining_mapping_itemnumber_nonzero(): void {
  ```
- `public/grade/tests/component_gradeitems_test.php:282`
  ```php
  public function test_get_field_name_for_itemnumber_component_invalid_mapping_itemnumber_nonzero(): void {
  ```
- `public/grade/tests/component_gradeitems_test.php:294`
  ```php
  public static function get_field_name_for_itemname_provider(): array {
  ```
- `public/grade/tests/component_gradeitems_test.php:337`
  ```php
  public function test_get_field_name_for_itemname(string $itemname, string $fieldname, string $expected): void {
  ```
- `public/grade/tests/component_gradeitems_test.php:345`
  ```php
  public function test_get_field_name_for_itemname_invalid_itemname(): void {
  ```
- `public/grade/tests/component_gradeitems_test.php:356`
  ```php
  public function test_get_field_name_for_itemname_not_defining_mapping_empty_name(): void {
  ```
- `public/grade/tests/component_gradeitems_test.php:365`
  ```php
  public function test_get_field_name_for_itemname_not_defining_mapping_with_name(): void {
  ```
- `public/grade/tests/component_gradeitems_test.php:375`
  ```php
  public function test_get_field_name_for_itemname_invalid_mapping_empty_name(): void {
  ```
- `public/grade/tests/component_gradeitems_test.php:384`
  ```php
  public function test_get_field_name_for_itemname_invalid_mapping_with_name(): void {
  ```
- `public/grade/tests/component_gradeitems_test.php:396`
  ```php
  public static function get_itemname_from_itemnumber_provider(): array {
  ```
- `public/grade/tests/component_gradeitems_test.php:416`
  ```php
  public function test_get_itemname_from_itemnumber(int $itemnumber, string $expected): void {
  ```
- `public/grade/tests/component_gradeitems_test.php:424`
  ```php
  public function test_get_itemname_from_itemnumber_outcome_itemnumber(): void {
  ```
- `public/grade/tests/component_gradeitems_test.php:433`
  ```php
  public function test_get_itemname_from_itemnumber_invalid_itemnumber(): void {
  ```
- `public/grade/tests/component_gradeitems_test.php:443`
  ```php
  public function test_get_itemname_from_itemnumber_component_not_defining_mapping_itemnumber_zero(): void {
  ```
- `public/grade/tests/component_gradeitems_test.php:452`
  ```php
  public function test_get_itemname_from_itemnumber_component_not_defining_mapping_itemnumber_nonzero(): void {
  ```
- `public/grade/tests/component_gradeitems_test.php:462`
  ```php
  public function test_get_itemname_from_itemnumber_component_invalid_mapping_itemnumber_nonzero(): void {
  ```
- `public/grade/tests/component_gradeitems_test.php:474`
  ```php
  public static function get_itemnumber_from_itemname_provider(): array {
  ```
- `public/grade/tests/component_gradeitems_test.php:47`
  ```php
  public function test_get_itemname_mapping_for_component_does_not_exist(): void {
  ```
- `public/grade/tests/component_gradeitems_test.php:498`
  ```php
  public function test_get_itemnumber_from_itemname(string $itemname, int $expected): void {
  ```
- `public/grade/tests/component_gradeitems_test.php:506`
  ```php
  public function test_get_itemnumber_from_itemname_invalid_itemname(): void {
  ```
- `public/grade/tests/component_gradeitems_test.php:517`
  ```php
  public function test_get_itemnumber_from_itemname_not_defining_mapping_empty_name(): void {
  ```
- `public/grade/tests/component_gradeitems_test.php:526`
  ```php
  public function test_get_itemnumber_from_itemname_not_defining_mapping_with_name(): void {
  ```
- `public/grade/tests/component_gradeitems_test.php:536`
  ```php
  public function test_get_itemnumber_from_itemname_invalid_mapping_empty_name(): void {
  ```
- `public/grade/tests/component_gradeitems_test.php:545`
  ```php
  public function test_get_itemnumber_from_itemname_invalid_mapping_with_name(): void {
  ```
- `public/grade/tests/component_gradeitems_test.php:571`
  ```php
  public static function get_itemname_mapping_for_component(): array {
  ```
- `public/grade/tests/component_gradeitems_test.php:57`
  ```php
  public function test_get_itemname_mapping_for_valid_component_invalid_mapping(): void {
  ```
- `public/grade/tests/component_gradeitems_test.php:598`
  ```php
  public static function get_itemname_mapping_for_component(): array {
  ```
- `public/grade/tests/component_gradeitems_test.php:610`
  ```php
  public static function get_advancedgrading_itemnames(): array {
  ```
- `public/grade/tests/component_gradeitems_test.php:635`
  ```php
  public static function get_itemname_mapping_for_component(): array {
  ```
- `public/grade/tests/component_gradeitems_test.php:647`
  ```php
  public static function get_advancedgrading_itemnames(): array {
  ```
- `public/grade/tests/component_gradeitems_test.php:65`
  ```php
  public function test_get_itemname_mapping_for_valid_component_valid_mapping(): void {
  ```
- `public/grade/tests/component_gradeitems_test.php:79`
  ```php
  public static function is_valid_itemname_provider(): array {
  ```
- `public/grade/tests/edittreelib_test.php:36`
  ```php
  public function test_format_number(): void {
  ```
- `public/grade/tests/edittreelib_test.php:46`
  ```php
  public function test_grade_edit_tree_column_range_get_item_cell(): void {
  ```
- `public/grade/tests/event/events_test.php:113`
  ```php
  public function test_grade_letter_updated(): void {
  ```
- `public/grade/tests/event/events_test.php:134`
  ```php
  public function test_scale_created(): void {
  ```
- `public/grade/tests/event/events_test.php:160`
  ```php
  public function test_scale_deleted(): void {
  ```
- `public/grade/tests/event/events_test.php:183`
  ```php
  public function test_scale_updated(): void {
  ```
- `public/grade/tests/event/events_test.php:51`
  ```php
  public function setUp(): void {
  ```
- `public/grade/tests/event/events_test.php:65`
  ```php
  public function test_grade_letter_created(): void {
  ```
- `public/grade/tests/event/events_test.php:89`
  ```php
  public function test_grade_letter_deleted(): void {
  ```
- `public/grade/tests/export_test.php:130`
  ```php
  public static function format_feedback_provider(): array {
  ```
- `public/grade/tests/export_test.php:43`
  ```php
  public function test_format_feedback($input, $inputformat, $expected): void {
  ```
- `public/grade/tests/export_test.php:63`
  ```php
  public function test_format_feedback_with_grade(): void {
  ```
- `public/grade/tests/external/create_gradecategories_test.php:37`
  ```php
  public function test_create_gradecategories(): void {
  ```
- `public/grade/tests/external/get_enrolled_users_for_selector_test.php:41`
  ```php
  public function test_get_enrolled_users_for_selector(): void {
  ```
- `public/grade/tests/external/get_feedback_test.php:105`
  ```php
  public function test_get_feedback_invalid_request(string $loggeduserrole, bool $feedbacknotincourse,
  ```
- `public/grade/tests/external/get_feedback_test.php:149`
  ```php
  public static function get_feedback_invalid_request_provider(): array {
  ```
- `public/grade/tests/external/get_feedback_test.php:38`
  ```php
  public function test_get_feedback(?string $feedback, array $expected): void {
  ```
- `public/grade/tests/external/get_feedback_test.php:73`
  ```php
  public static function get_feedback_provider(): array {
  ```
- `public/grade/tests/external/get_gradable_users_test.php:38`
  ```php
  public function test_execute(bool $onlyactiveenrol, bool $grouprestricted, array $expected): void {
  ```
- `public/grade/tests/external/get_gradable_users_test.php:97`
  ```php
  public static function execute_data(): array {
  ```
- `public/grade/tests/external/get_grade_tree_test.php:35`
  ```php
  public function test_execute(): void {
  ```
- `public/grade/tests/external/get_gradeitems_test.php:32`
  ```php
  public function test_execute(): void {
  ```
- `public/grade/tests/grades/grader/gradingpanel/point/external/fetch_test.php:140`
  ```php
  public function test_execute_fetch_graded(): void {
  ```
- `public/grade/tests/grades/grader/gradingpanel/point/external/fetch_test.php:157`
  ```php
  public function test_execute_fetch_does_not_return_data_to_other_students(): void {
  ```
- `public/grade/tests/grades/grader/gradingpanel/point/external/fetch_test.php:176`
  ```php
  public function test_execute_fetch_return_data_to_graded_user(): void {
  ```
- `public/grade/tests/grades/grader/gradingpanel/point/external/fetch_test.php:193`
  ```php
  private function execute_and_assert_fetch($forum, $fetcheruser, $grader, $gradeduser) {
  ```
- `public/grade/tests/grades/grader/gradingpanel/point/external/fetch_test.php:246`
  ```php
  protected function get_forum_instance(array $config = []): forum_entity {
  ```
- `public/grade/tests/grades/grader/gradingpanel/point/external/fetch_test.php:41`
  ```php
  public function test_execute_invalid_component(): void {
  ```
- `public/grade/tests/grades/grader/gradingpanel/point/external/fetch_test.php:54`
  ```php
  public function test_execute_invalid_itemname(): void {
  ```
- `public/grade/tests/grades/grader/gradingpanel/point/external/fetch_test.php:67`
  ```php
  public function test_execute_incorrect_type(): void {
  ```
- `public/grade/tests/grades/grader/gradingpanel/point/external/fetch_test.php:89`
  ```php
  public function test_execute_fetch_empty(): void {
  ```
- `public/grade/tests/grades/grader/gradingpanel/point/external/store_test.php:110`
  ```php
  public function test_execute_store_empty(): void {
  ```
- `public/grade/tests/grades/grader/gradingpanel/point/external/store_test.php:197`
  ```php
  public function test_execute_store_graded(): void {
  ```
- `public/grade/tests/grades/grader/gradingpanel/point/external/store_test.php:273`
  ```php
  public function test_execute_store_out_of__range(int $maxvalue, float $suppliedvalue): void {
  ```
- `public/grade/tests/grades/grader/gradingpanel/point/external/store_test.php:302`
  ```php
  public static function execute_out_of_range_provider(): array {
  ```
- `public/grade/tests/grades/grader/gradingpanel/point/external/store_test.php:330`
  ```php
  protected function get_forum_instance(array $config = []): forum_entity {
  ```
- `public/grade/tests/grades/grader/gradingpanel/point/external/store_test.php:43`
  ```php
  public function test_execute_invalid_component(): void {
  ```
- `public/grade/tests/grades/grader/gradingpanel/point/external/store_test.php:56`
  ```php
  public function test_execute_invalid_itemname(): void {
  ```
- `public/grade/tests/grades/grader/gradingpanel/point/external/store_test.php:69`
  ```php
  public function test_execute_incorrect_type(): void {
  ```
- `public/grade/tests/grades/grader/gradingpanel/point/external/store_test.php:91`
  ```php
  public function test_execute_disabled(): void {
  ```
- `public/grade/tests/grades/grader/gradingpanel/scale/external/fetch_test.php:161`
  ```php
  public function test_execute_fetch_graded(): void {
  ```
- `public/grade/tests/grades/grader/gradingpanel/scale/external/fetch_test.php:185`
  ```php
  public function test_execute_fetch_does_not_return_data_to_other_students(): void {
  ```
- `public/grade/tests/grades/grader/gradingpanel/scale/external/fetch_test.php:211`
  ```php
  public function test_execute_fetch_return_data_to_graded_user(): void {
  ```
- `public/grade/tests/grades/grader/gradingpanel/scale/external/fetch_test.php:235`
  ```php
  private function execute_and_assert_fetch($forum, $options, $scale, $fetcheruser, $grader, $gradeduser) {
  ```
- `public/grade/tests/grades/grader/gradingpanel/scale/external/fetch_test.php:312`
  ```php
  protected function get_forum_instance(array $config = []): forum_entity {
  ```
- `public/grade/tests/grades/grader/gradingpanel/scale/external/fetch_test.php:41`
  ```php
  public function test_execute_invalid_component(): void {
  ```
- `public/grade/tests/grades/grader/gradingpanel/scale/external/fetch_test.php:54`
  ```php
  public function test_execute_invalid_itemname(): void {
  ```
- `public/grade/tests/grades/grader/gradingpanel/scale/external/fetch_test.php:67`
  ```php
  public function test_execute_incorrect_type(): void {
  ```
- `public/grade/tests/grades/grader/gradingpanel/scale/external/fetch_test.php:89`
  ```php
  public function test_execute_fetch_empty(): void {
  ```
- `public/grade/tests/grades/grader/gradingpanel/scale/external/store_test.php:110`
  ```php
  public function test_execute_store_empty(): void {
  ```
- `public/grade/tests/grades/grader/gradingpanel/scale/external/store_test.php:194`
  ```php
  public function test_execute_store_not_selected(): void {
  ```
- `public/grade/tests/grades/grader/gradingpanel/scale/external/store_test.php:279`
  ```php
  public function test_execute_store_graded(): void {
  ```
- `public/grade/tests/grades/grader/gradingpanel/scale/external/store_test.php:373`
  ```php
  public function test_execute_store_out_of_range(int $suppliedvalue): void {
  ```
- `public/grade/tests/grades/grader/gradingpanel/scale/external/store_test.php:401`
  ```php
  public static function execute_out_of_range_provider(): array {
  ```
- `public/grade/tests/grades/grader/gradingpanel/scale/external/store_test.php:425`
  ```php
  protected function get_forum_instance(array $config = []): forum_entity {
  ```
- `public/grade/tests/grades/grader/gradingpanel/scale/external/store_test.php:43`
  ```php
  public function test_execute_invalid_component(): void {
  ```
- `public/grade/tests/grades/grader/gradingpanel/scale/external/store_test.php:443`
  ```php
  protected function get_test_data(): array {
  ```
- `public/grade/tests/grades/grader/gradingpanel/scale/external/store_test.php:56`
  ```php
  public function test_execute_invalid_itemname(): void {
  ```
- `public/grade/tests/grades/grader/gradingpanel/scale/external/store_test.php:69`
  ```php
  public function test_execute_incorrect_type(): void {
  ```
- `public/grade/tests/grades/grader/gradingpanel/scale/external/store_test.php:91`
  ```php
  public function test_execute_disabled(): void {
  ```
- `public/grade/tests/importlib_test.php:216`
  ```php
  public function test_grade_import_commit_unenrolled_user(): void {
  ```
- `public/grade/tests/importlib_test.php:252`
  ```php
  public function test_get_unenrolled_users_in_import(): void {
  ```
- `public/grade/tests/importlib_test.php:34`
  ```php
  public static function setUpBeforeClass(): void {
  ```
- `public/grade/tests/importlib_test.php:47`
  ```php
  private function import_grades($data) {
  ```
- `public/grade/tests/importlib_test.php:84`
  ```php
  public function test_grade_import_commit(): void {
  ```
- `public/grade/tests/lib_test.php:185`
  ```php
  public function test_ungraded_counts_count_sumgrades(): void {
  ```
- `public/grade/tests/lib_test.php:380`
  ```php
  public function test_ungraded_counts_hidden_grades(bool $hidden, array $expectedcount, array $expectedsumarray): void {
  ```
- `public/grade/tests/lib_test.php:460`
  ```php
  public static function ungraded_counts_hidden_grades_data(): array {
  ```
- `public/grade/tests/lib_test.php:480`
  ```php
  public function test_ungraded_count_sumgrades_groups(): void {
  ```
- `public/grade/tests/lib_test.php:51`
  ```php
  public function test_can_output_item(): void {
  ```
- `public/grade/tests/lib_test.php:642`
  ```php
  public function test_ungraded_counts_only_active_enrol(bool $onlyactive,
  ```
- `public/grade/tests/lib_test.php:733`
  ```php
  public static function ungraded_counts_only_active_enrol_data(): array {
  ```
- `public/grade/tests/lib_test.php:796`
  ```php
  public function test_calculate_average(int $meanselection, array $expectedmeancount, array $expectedaverage): void {
  ```
- `public/grade/tests/lib_test.php:881`
  ```php
  public static function calculate_average_data(): array {
  ```
- `public/grade/tests/lib_test.php:905`
  ```php
  public function test_item_types(): void {
  ```
- `public/grade/tests/lib_test.php:979`
  ```php
  public function test_get_gradable_users(): void {
  ```
- `public/grade/tests/output/general_action_bar_test.php:159`
  ```php
  public static function export_for_template_provider(): array {
  ```
- `public/grade/tests/output/general_action_bar_test.php:36`
  ```php
  public static function setUpBeforeClass(): void {
  ```
- `public/grade/tests/output/general_action_bar_test.php:49`
  ```php
  protected function find_option_by_name(array $options, string $name): ?array {
  ```
- `public/grade/tests/output/general_action_bar_test.php:67`
  ```php
  public function test_export_for_template(string $userrole, bool $enableoutcomes, array $expectedoptions): void {
  ```
- `public/grade/tests/output/penalty_indicator_test.php:112`
  ```php
  public function test_export_for_template(
  ```
- `public/grade/tests/output/penalty_indicator_test.php:36`
  ```php
  public static function export_for_template_provider(): array {
  ```
- `public/grade/tests/penalty_manager_test.php:115`
  ```php
  public function test_penalty_applied_before_grade_factors(): void {
  ```
- `public/grade/tests/penalty_manager_test.php:179`
  ```php
  public function test_apply_grade_penalty_with_due_date_extension(): void {
  ```
- `public/grade/tests/penalty_manager_test.php:252`
  ```php
  public function test_full_regrade_preserves_penalised_finalgrade(): void {
  ```
- `public/grade/tests/penalty_manager_test.php:36`
  ```php
  public function test_is_penalty_enabled_for_module(): void {
  ```
- `public/grade/tests/penalty_manager_test.php:72`
  ```php
  public function test_apply_grade_penalty_to_user(): void {
  ```
- `public/grade/tests/privacy/provider_test.php:1287`
  ```php
  public function test_export_data_for_user_with_scale(): void {
  ```
- `public/grade/tests/privacy/provider_test.php:1320`
  ```php
  public function test_export_data_for_user_about_gradebook_edits(): void {
  ```
- `public/grade/tests/privacy/provider_test.php:1574`
  ```php
  protected function assert_context_has_no_data(\context $context) {
  ```
- `public/grade/tests/privacy/provider_test.php:1594`
  ```php
  private function add_feedback_file_to_copy() {
  ```
- `public/grade/tests/privacy/provider_test.php:183`
  ```php
  public function test_get_contexts_for_userid_grades_and_history(): void {
  ```
- `public/grade/tests/privacy/provider_test.php:257`
  ```php
  public function test_get_users_in_context_gradebook_edits(): void {
  ```
- `public/grade/tests/privacy/provider_test.php:352`
  ```php
  public function test_get_users_in_context_grades_and_history(): void {
  ```
- `public/grade/tests/privacy/provider_test.php:408`
  ```php
  public function test_delete_data_for_all_users_in_context(): void {
  ```
- `public/grade/tests/privacy/provider_test.php:51`
  ```php
  public function setUp(): void {
  ```
- `public/grade/tests/privacy/provider_test.php:58`
  ```php
  public function test_get_contexts_for_userid_gradebook_edits(): void {
  ```
- `public/grade/tests/privacy/provider_test.php:648`
  ```php
  public function test_delete_data_for_user(): void {
  ```
- `public/grade/tests/privacy/provider_test.php:899`
  ```php
  public function test_delete_data_for_users(): void {
  ```
- `public/grade/tests/privacy/provider_test.php:970`
  ```php
  public function test_export_data_for_user_about_grades_and_history(): void {
  ```
- `public/grade/tests/querylib_test.php:35`
  ```php
  public function test_grade_get_gradable_activities(): void {
  ```
- `public/grade/tests/querylib_test.php:54`
  ```php
  public function test_grade_get_grade_items_for_activity(): void {
  ```
- `public/grade/tests/report_graderlib_test.php:115`
  ```php
  public function test_collapsed_preferences(): void {
  ```
- `public/grade/tests/report_graderlib_test.php:242`
  ```php
  public function test_old_collapsed_preferences(): void {
  ```
- `public/grade/tests/report_graderlib_test.php:45`
  ```php
  public function test_process_data(): void {
  ```
- `public/grade/tests/report_graderlib_test.php:462`
  ```php
  public function test_get_right_rows(): void {
  ```
- `public/grade/tests/report_graderlib_test.php:528`
  ```php
  public function test_load_users_paging_preference(): void {
  ```
- `public/grade/tests/report_graderlib_test.php:554`
  ```php
  public function test_get_students_per_page(): void {
  ```
- `public/grade/tests/report_graderlib_test.php:566`
  ```php
  private function create_grade_category($course) {
  ```
- `public/grade/tests/report_graderlib_test.php:576`
  ```php
  private function create_report($course) {
  ```
- `public/grade/tests/reportlib_test.php:36`
  ```php
  public function __construct($courseid, $gpr, $context, $user) {
  ```
- `public/grade/tests/reportlib_test.php:44`
  ```php
  public function blank_hidden_total_and_adjust_bounds($courseid, $courseitem, $finalgrade) {
  ```
- `public/grade/tests/reportlib_test.php:51`
  ```php
  public function process_data($data) {
  ```
- `public/grade/tests/reportlib_test.php:57`
  ```php
  public function process_action($target, $action) {
  ```
- `public/grade/tests/reportlib_test.php:69`
  ```php
  public function test_blank_hidden_total_and_adjust_bounds(): void {
  ```
- `public/grade/tests/reportuserlib_test.php:176`
  ```php
  private function create_report($course, $user, $coursecontext) {
  ```
- `public/grade/tests/reportuserlib_test.php:44`
  ```php
  public function test_inject_rowspans(): void {
  ```
- `public/lib/antivirus/clamav/db/upgrade.php:31`
  ```php
  function xmldb_antivirus_clamav_upgrade($oldversion) {
  ```
- `public/lib/classes/check/environment/upgradecheck.php:47`
  ```php
  public function get_name(): string {
  ```
- `public/lib/classes/check/environment/upgradecheck.php:56`
  ```php
  public function get_action_link(): ?\action_link {
  ```
- `public/lib/classes/check/environment/upgradecheck.php:66`
  ```php
  public function get_result(): result {
  ```
- `public/lib/classes/event/grade_deleted.php:108`
  ```php
  public function get_description() {
  ```
- `public/lib/classes/event/grade_deleted.php:119`
  ```php
  protected function validate_data() {
  ```
- `public/lib/classes/event/grade_deleted.php:135`
  ```php
  public static function get_objectid_mapping() {
  ```
- `public/lib/classes/event/grade_deleted.php:139`
  ```php
  public static function get_other_mapping() {
  ```
- `public/lib/classes/event/grade_deleted.php:55`
  ```php
  protected function init() {
  ```
- `public/lib/classes/event/grade_deleted.php:67`
  ```php
  public static function create_from_grade(\grade_grade $grade) {
  ```
- `public/lib/classes/event/grade_deleted.php:87`
  ```php
  public function get_grade() {
  ```
- `public/lib/classes/event/grade_deleted.php:99`
  ```php
  public static function get_name() {
  ```
- `public/lib/classes/event/grade_exported.php:32`
  ```php
  protected function init() {
  ```
- `public/lib/classes/event/grade_exported.php:45`
  ```php
  public static function get_export_type() {
  ```
- `public/lib/classes/event/grade_exported.php:56`
  ```php
  public static function get_name() {
  ```
- `public/lib/classes/event/grade_exported.php:71`
  ```php
  public function get_description() {
  ```
- `public/lib/classes/event/grade_exported.php:82`
  ```php
  public function get_url() {
  ```
- `public/lib/classes/event/grade_item_created.php:108`
  ```php
  public function get_description() {
  ```
- `public/lib/classes/event/grade_item_created.php:118`
  ```php
  public function get_url() {
  ```
- `public/lib/classes/event/grade_item_created.php:130`
  ```php
  protected function validate_data() {
  ```
- `public/lib/classes/event/grade_item_created.php:47`
  ```php
  protected function init() {
  ```
- `public/lib/classes/event/grade_item_created.php:58`
  ```php
  public static function get_name() {
  ```
- `public/lib/classes/event/grade_item_created.php:68`
  ```php
  public static function create_from_grade_item(\grade_item $gradeitem) {
  ```
- `public/lib/classes/event/grade_item_created.php:91`
  ```php
  public function get_grade_item() {
  ```
- `public/lib/classes/event/grade_item_deleted.php:41`
  ```php
  protected function init() {
  ```
- `public/lib/classes/event/grade_item_deleted.php:52`
  ```php
  public static function get_name() {
  ```
- `public/lib/classes/event/grade_item_deleted.php:61`
  ```php
  public function get_description() {
  ```
- `public/lib/classes/event/grade_item_updated.php:44`
  ```php
  protected function init() {
  ```
- `public/lib/classes/event/grade_item_updated.php:55`
  ```php
  public static function get_name() {
  ```
- `public/lib/classes/event/grade_item_updated.php:64`
  ```php
  public function get_description() {
  ```
- `public/lib/classes/event/grade_letter_created.php:44`
  ```php
  protected function init() {
  ```
- `public/lib/classes/event/grade_letter_created.php:55`
  ```php
  public static function get_name() {
  ```
- `public/lib/classes/event/grade_letter_created.php:64`
  ```php
  public function get_description() {
  ```
- `public/lib/classes/event/grade_letter_created.php:77`
  ```php
  public function get_url() {
  ```
- `public/lib/classes/event/grade_letter_created.php:89`
  ```php
  public static function get_objectid_mapping() {
  ```
- `public/lib/classes/event/grade_letter_deleted.php:44`
  ```php
  protected function init() {
  ```
- `public/lib/classes/event/grade_letter_deleted.php:55`
  ```php
  public static function get_name() {
  ```
- `public/lib/classes/event/grade_letter_deleted.php:64`
  ```php
  public function get_description() {
  ```
- `public/lib/classes/event/grade_letter_deleted.php:78`
  ```php
  public static function get_objectid_mapping() {
  ```
- `public/lib/classes/event/grade_letter_updated.php:44`
  ```php
  protected function init() {
  ```
- `public/lib/classes/event/grade_letter_updated.php:55`
  ```php
  public static function get_name() {
  ```
- `public/lib/classes/event/grade_letter_updated.php:64`
  ```php
  public function get_description() {
  ```
- `public/lib/classes/event/grade_letter_updated.php:78`
  ```php
  public function get_url() {
  ```
- `public/lib/classes/event/grade_letter_updated.php:91`
  ```php
  public static function get_objectid_mapping() {
  ```
- `public/lib/classes/event/grade_report_viewed.php:45`
  ```php
  protected function init() {
  ```
- `public/lib/classes/event/grade_report_viewed.php:59`
  ```php
  public static function get_name() {
  ```
- `public/lib/classes/event/grade_report_viewed.php:68`
  ```php
  public function get_description() {
  ```
- `public/lib/classes/event/grade_report_viewed.php:76`
  ```php
  public function get_url() {
  ```
- `public/lib/classes/event/grade_report_viewed.php:86`
  ```php
  protected function validate_data() {
  ```
- `public/lib/classes/event/user_graded.php:107`
  ```php
  public static function get_name() {
  ```
- `public/lib/classes/event/user_graded.php:116`
  ```php
  public function get_description() {
  ```
- `public/lib/classes/event/user_graded.php:126`
  ```php
  public function get_url() {
  ```
- `public/lib/classes/event/user_graded.php:140`
  ```php
  protected function validate_data() {
  ```
- `public/lib/classes/event/user_graded.php:152`
  ```php
  public static function get_objectid_mapping() {
  ```
- `public/lib/classes/event/user_graded.php:156`
  ```php
  public static function get_other_mapping() {
  ```
- `public/lib/classes/event/user_graded.php:60`
  ```php
  public static function create_from_grade(\grade_grade $grade, $userid = null) {
  ```
- `public/lib/classes/event/user_graded.php:84`
  ```php
  public function get_grade() {
  ```
- `public/lib/classes/event/user_graded.php:96`
  ```php
  protected function init() {
  ```
- `public/lib/classes/grades_external.php:108`
  ```php
  public static function update_grades($source, $courseid, $component, $activityid,
  ```
- `public/lib/classes/grades_external.php:184`
  ```php
  public static function update_grades_returns() {
  ```
- `public/lib/classes/grades_external.php:49`
  ```php
  public static function update_grades_parameters() {
  ```
- `public/lib/classes/plugininfo/gradeexport.php:33`
  ```php
  public function is_uninstall_allowed() {
  ```
- `public/lib/classes/plugininfo/gradeimport.php:33`
  ```php
  public function is_uninstall_allowed() {
  ```
- `public/lib/classes/plugininfo/gradepenalty.php:31`
  ```php
  public function is_uninstall_allowed(): bool {
  ```
- `public/lib/classes/plugininfo/gradepenalty.php:36`
  ```php
  public static function get_manage_url(): url {
  ```
- `public/lib/classes/plugininfo/gradepenalty.php:41`
  ```php
  public static function plugintype_supports_disabling(): bool {
  ```
- `public/lib/classes/plugininfo/gradepenalty.php:46`
  ```php
  public static function get_enabled_plugins(): array {
  ```
- `public/lib/classes/plugininfo/gradepenalty.php:55`
  ```php
  public static function enable_plugin(string $pluginname, int $enabled): bool {
  ```
- `public/lib/classes/plugininfo/gradepenalty.php:76`
  ```php
  public function is_enabled(): bool {
  ```
- `public/lib/classes/plugininfo/gradepenalty.php:86`
  ```php
  public static function is_plugin_enabled(string $pluginname): bool {
  ```
- `public/lib/classes/plugininfo/gradepenalty.php:94`
  ```php
  public function get_settings_section_name(): string {
  ```
- `public/lib/classes/plugininfo/gradepenalty.php:99`
  ```php
  public function get_settings_url(): ?url {
  ```
- `public/lib/classes/plugininfo/gradereport.php:33`
  ```php
  public function is_uninstall_allowed() {
  ```
- `public/lib/classes/task/grade_cron_task.php:36`
  ```php
  public function get_name() {
  ```
- `public/lib/classes/task/grade_cron_task.php:44`
  ```php
  public function execute() {
  ```
- `public/lib/classes/task/grade_history_cleanup_task.php:40`
  ```php
  public function get_name() {
  ```
- `public/lib/classes/task/grade_history_cleanup_task.php:47`
  ```php
  public function execute() {
  ```
- `public/lib/db/upgrade.php:85`
  ```php
  function xmldb_main_upgrade($oldversion) {
  ```
- `public/lib/db/upgradelib.php:1019`
  ```php
  function upgrade_calendar_action_events_fix(stdClass $info, bool $output = true, int $endtime = 0): bool {
  ```
- `public/lib/db/upgradelib.php:1059`
  ```php
  function upgrade_calendar_override_events_fix(stdClass $info, bool $output = true, int $endtime = 0): bool {
  ```
- `public/lib/db/upgradelib.php:1136`
  ```php
  function upgrade_add_item_to_usermenu(string $menuitem): void {
  ```
- `public/lib/db/upgradelib.php:1163`
  ```php
  function upgrade_block_set_defaultregion(
  ```
- `public/lib/db/upgradelib.php:1270`
  ```php
  function upgrade_block_delete_instances(
  ```
- `public/lib/db/upgradelib.php:1393`
  ```php
  function upgrade_block_set_my_user_parent_context(
  ```
- `public/lib/db/upgradelib.php:143`
  ```php
  function upgrade_extra_credit_weightoverride($onlycourseid = 0) {
  ```
- `public/lib/db/upgradelib.php:1463`
  ```php
  function upgrade_fix_file_timestamps() {
  ```
- `public/lib/db/upgradelib.php:1497`
  ```php
  function upgrade_add_foreign_key_and_indexes() {
  ```
- `public/lib/db/upgradelib.php:178`
  ```php
  function upgrade_calculated_grade_items($courseid = null) {
  ```
- `public/lib/db/upgradelib.php:1851`
  ```php
  function upgrade_change_binary_column_to_int(
  ```
- `public/lib/db/upgradelib.php:1901`
  ```php
  function upgrade_store_relative_url_sitehomepage() {
  ```
- `public/lib/db/upgradelib.php:1923`
  ```php
  function upgrade_convert_ai_providers_to_instances() {
  ```
- `public/lib/db/upgradelib.php:2051`
  ```php
  function upgrade_add_explain_action_to_ai_providers() {
  ```
- `public/lib/db/upgradelib.php:2096`
  ```php
  function upgrade_create_async_mimetype_upgrade_task(string $mimetype, array $extensions): void {
  ```
- `public/lib/db/upgradelib.php:2120`
  ```php
  function moodlenet_migrate_profile_field(): void {
  ```
- `public/lib/db/upgradelib.php:278`
  ```php
  function make_default_scale() {
  ```
- `public/lib/db/upgradelib.php:302`
  ```php
  function make_competence_scale() {
  ```
- `public/lib/db/upgradelib.php:329`
  ```php
  function upgrade_course_letter_boundary($courseid = null) {
  ```
- `public/lib/db/upgradelib.php:39`
  ```php
  function upgrade_mysql_get_supported_tables() {
  ```
- `public/lib/db/upgradelib.php:435`
  ```php
  function upgrade_letter_boundary_needs_freeze($context) {
  ```
- `public/lib/db/upgradelib.php:472`
  ```php
  function upgrade_standardise_score($rawgrade, $sourcemin, $sourcemax, $targetmin, $targetmax) {
  ```
- `public/lib/db/upgradelib.php:495`
  ```php
  function upgrade_fix_serialized_objects($serializeddata) {
  ```
- `public/lib/db/upgradelib.php:508`
  ```php
  function upgrade_delete_orphaned_file_records() {
  ```
- `public/lib/db/upgradelib.php:534`
  ```php
  function upgrade_core_licenses() {
  ```
- `public/lib/db/upgradelib.php:612`
  ```php
  function upgrade_calendar_site_status(bool $output = true): bool {
  ```
- `public/lib/db/upgradelib.php:684`
  ```php
  function upgrade_calendar_events_status(bool $output = true): array {
  ```
- `public/lib/db/upgradelib.php:821`
  ```php
  function upgrade_calendar_events_fix_remaining(array $info, bool $output = true, int $maxseconds = 0): bool {
  ```
- `public/lib/db/upgradelib.php:873`
  ```php
  function upgrade_calendar_events_mtrace(string $string, bool $output): void {
  ```
- `public/lib/db/upgradelib.php:910`
  ```php
  function upgrade_calendar_events_get_teacherid(int $courseid): int {
  ```
- `public/lib/db/upgradelib.php:91`
  ```php
  function upgrade_group_members_only($groupingid, $availability) {
  ```
- `public/lib/db/upgradelib.php:928`
  ```php
  function upgrade_calendar_standard_events_fix(stdClass $info, bool $output = true, int $endtime = 0): bool {
  ```
- `public/lib/db/upgradelib.php:978`
  ```php
  function upgrade_calendar_subscription_events_fix(stdClass $info, bool $output = true, int $endtime = 0): bool {
  ```
- `public/lib/editor/tiny/plugins/premium/db/upgrade.php:32`
  ```php
  function xmldb_tiny_premium_upgrade($oldversion) {
  ```
- `public/lib/editor/tiny/plugins/recordrtc/db/upgrade.php:30`
  ```php
  function xmldb_tiny_recordrtc_upgrade($oldversion) {
  ```
- `public/lib/form/modgrade.php:125`
  ```php
  public function _createElements() {
  ```
- `public/lib/form/modgrade.php:252`
  ```php
  public function exportValue(&$submitvalues, $notused = false) {
  ```
- `public/lib/form/modgrade.php:282`
  ```php
  protected function process_value($type='none', $scale=null, $point=null, $rescalegrades=null) {
  ```
- `public/lib/form/modgrade.php:320`
  ```php
  protected function validate_scale($val) {
  ```
- `public/lib/form/modgrade.php:332`
  ```php
  protected function validate_point($val) {
  ```
- `public/lib/form/modgrade.php:349`
  ```php
  public function onQuickFormEvent($event, $arg, &$caller) {
  ```
- `public/lib/form/modgrade.php:537`
  ```php
  protected function generate_modgrade_subelement_id($subname) {
  ```
- `public/lib/form/modgrade.php:90`
  ```php
  public function __construct($elementname = null, $elementlabel = null, $options = array(), $attributes = null) {
  ```
- `public/lib/grade/grade_category.php:1028`
  ```php
  public function aggregate_values_and_adjust_bounds($grade_values,
  ```
- `public/lib/grade/grade_category.php:1504`
  ```php
  public function aggregate_values($grade_values, $items) {
  ```
- `public/lib/grade/grade_category.php:1518`
  ```php
  private function auto_update_max() {
  ```
- `public/lib/grade/grade_category.php:1605`
  ```php
  private function auto_update_weights() {
  ```
- `public/lib/grade/grade_category.php:1781`
  ```php
  public function apply_limit_rules(&$grade_values, $items) {
  ```
- `public/lib/grade/grade_category.php:178`
  ```php
  public static function build_path($grade_category) {
  ```
- `public/lib/grade/grade_category.php:1881`
  ```php
  public function can_apply_limit_rules() {
  ```
- `public/lib/grade/grade_category.php:1941`
  ```php
  public function is_extracredit_used() {
  ```
- `public/lib/grade/grade_category.php:1951`
  ```php
  public static function aggregation_uses_extracredit($aggregation) {
  ```
- `public/lib/grade/grade_category.php:1962`
  ```php
  public function is_aggregationcoef_used() {
  ```
- `public/lib/grade/grade_category.php:196`
  ```php
  public static function fetch($params) {
  ```
- `public/lib/grade/grade_category.php:1973`
  ```php
  public static function aggregation_uses_aggregationcoef($aggregation) {
  ```
- `public/lib/grade/grade_category.php:1987`
  ```php
  public function get_coefstring($first=true) {
  ```
- `public/lib/grade/grade_category.php:2042`
  ```php
  public static function fetch_course_tree($courseid, $include_category_items=false) {
  ```
- `public/lib/grade/grade_category.php:2059`
  ```php
  private static function _fetch_course_tree_recursion($category_array, &$sortorder) {
  ```
- `public/lib/grade/grade_category.php:2110`
  ```php
  public function get_children($include_category_items=false) {
  ```
- `public/lib/grade/grade_category.php:221`
  ```php
  public static function fetch_all($params) {
  ```
- `public/lib/grade/grade_category.php:2222`
  ```php
  private static function _get_children_recursion($category) {
  ```
- `public/lib/grade/grade_category.php:2262`
  ```php
  public function load_grade_item() {
  ```
- `public/lib/grade/grade_category.php:2276`
  ```php
  public function get_grade_item() {
  ```
- `public/lib/grade/grade_category.php:2313`
  ```php
  public function load_parent_category() {
  ```
- `public/lib/grade/grade_category.php:2325`
  ```php
  public function get_parent_category() {
  ```
- `public/lib/grade/grade_category.php:2340`
  ```php
  public function get_name($escape = true) {
  ```
- `public/lib/grade/grade_category.php:2361`
  ```php
  public function get_description() {
  ```
- `public/lib/grade/grade_category.php:2390`
  ```php
  public function set_parent($parentid, $source=null) {
  ```
- `public/lib/grade/grade_category.php:239`
  ```php
  public function update($source = null, $isbulkupdate = false) {
  ```
- `public/lib/grade/grade_category.php:2426`
  ```php
  public function get_final($userid=null) {
  ```
- `public/lib/grade/grade_category.php:2438`
  ```php
  public function get_sortorder() {
  ```
- `public/lib/grade/grade_category.php:2450`
  ```php
  public function get_idnumber() {
  ```
- `public/lib/grade/grade_category.php:2462`
  ```php
  public function set_sortorder($sortorder) {
  ```
- `public/lib/grade/grade_category.php:2475`
  ```php
  public function move_after_sortorder($sortorder) {
  ```
- `public/lib/grade/grade_category.php:2485`
  ```php
  public function is_course_category() {
  ```
- `public/lib/grade/grade_category.php:2496`
  ```php
  public static function fetch_course_category($courseid) {
  ```
- `public/lib/grade/grade_category.php:2519`
  ```php
  public function is_editable() {
  ```
- `public/lib/grade/grade_category.php:2530`
  ```php
  public function is_locked() {
  ```
- `public/lib/grade/grade_category.php:2545`
  ```php
  public function set_locked($lockedstate, $cascade=false, $refresh=true) {
  ```
- `public/lib/grade/grade_category.php:2577`
  ```php
  public static function set_properties(&$instance, $params) {
  ```
- `public/lib/grade/grade_category.php:2621`
  ```php
  public function set_hidden($hidden, $cascade=false) {
  ```
- `public/lib/grade/grade_category.php:2664`
  ```php
  public function apply_default_settings() {
  ```
- `public/lib/grade/grade_category.php:2684`
  ```php
  public function apply_forced_settings() {
  ```
- `public/lib/grade/grade_category.php:2710`
  ```php
  public static function updated_forced_settings() {
  ```
- `public/lib/grade/grade_category.php:2723`
  ```php
  public static function get_default_aggregation_coefficient_values($aggregationmethod) {
  ```
- `public/lib/grade/grade_category.php:2753`
  ```php
  protected function notify_changed($deleted) {
  ```
- `public/lib/grade/grade_category.php:2766`
  ```php
  protected static function generate_record_set_key($params) {
  ```
- `public/lib/grade/grade_category.php:2776`
  ```php
  protected static function retrieve_record_set($params) {
  ```
- `public/lib/grade/grade_category.php:2788`
  ```php
  protected static function set_record_set($params, $records) {
  ```
- `public/lib/grade/grade_category.php:2801`
  ```php
  public static function clean_record_set() {
  ```
- `public/lib/grade/grade_category.php:294`
  ```php
  public function delete($source=null) {
  ```
- `public/lib/grade/grade_category.php:369`
  ```php
  public function insert($source = null, $isbulkupdate = false) {
  ```
- `public/lib/grade/grade_category.php:404`
  ```php
  public function insert_course_category($courseid) {
  ```
- `public/lib/grade/grade_category.php:434`
  ```php
  public function qualifies_for_regrading() {
  ```
- `public/lib/grade/grade_category.php:454`
  ```php
  public function force_regrading() {
  ```
- `public/lib/grade/grade_category.php:464`
  ```php
  public function pre_regrade_final_grades() {
  ```
- `public/lib/grade/grade_category.php:488`
  ```php
  public function generate_grades($userid=null, ?\core\progress\base $progress = null) {
  ```
- `public/lib/grade/grade_category.php:605`
  ```php
  private function aggregate_grades($userid,
  ```
- `public/lib/grade/grade_category.php:847`
  ```php
  private function set_usedinaggregation($userid, $usedweights, $novalue, $dropped, $extracredit) {
  ```
- `public/lib/grade/grade_grade.php:1044`
  ```php
  public function is_passed($grade_item = null) {
  ```
- `public/lib/grade/grade_grade.php:1079`
  ```php
  public function update($source=null, $isbulkupdate = false) {
  ```
- `public/lib/grade/grade_grade.php:1093`
  ```php
  protected function add_feedback_files(?int $historyid = null) {
  ```
- `public/lib/grade/grade_grade.php:1114`
  ```php
  protected function update_feedback_files(?int $historyid = null) {
  ```
- `public/lib/grade/grade_grade.php:1137`
  ```php
  protected function delete_feedback_files() {
  ```
- `public/lib/grade/grade_grade.php:1157`
  ```php
  public function delete($source = null) {
  ```
- `public/lib/grade/grade_grade.php:1181`
  ```php
  protected function notify_changed($deleted, $isbulkupdate = false) {
  ```
- `public/lib/grade/grade_grade.php:1252`
  ```php
  function get_aggregation_hint() {
  ```
- `public/lib/grade/grade_grade.php:1264`
  ```php
  private function copy_feedback_files(context $context, string $filearea, int $itemid) {
  ```
- `public/lib/grade/grade_grade.php:1292`
  ```php
  public function get_context() {
  ```
- `public/lib/grade/grade_grade.php:1302`
  ```php
  public function is_penalty_applied_to_final_grade(): bool {
  ```
- `public/lib/grade/grade_grade.php:238`
  ```php
  public static function fetch_users_grades($grade_item, $userids, $include_missing=true) {
  ```
- `public/lib/grade/grade_grade.php:279`
  ```php
  public function load_grade_item() {
  ```
- `public/lib/grade/grade_grade.php:306`
  ```php
  public function is_editable() {
  ```
- `public/lib/grade/grade_grade.php:331`
  ```php
  public function is_locked() {
  ```
- `public/lib/grade/grade_grade.php:345`
  ```php
  public function is_overridden() {
  ```
- `public/lib/grade/grade_grade.php:354`
  ```php
  public function get_datesubmitted() {
  ```
- `public/lib/grade/grade_grade.php:364`
  ```php
  public function get_aggregationweight() {
  ```
- `public/lib/grade/grade_grade.php:374`
  ```php
  public function set_aggregationweight($aggregationweight) {
  ```
- `public/lib/grade/grade_grade.php:384`
  ```php
  public function get_aggregationstatus() {
  ```
- `public/lib/grade/grade_grade.php:394`
  ```php
  public function set_aggregationstatus($aggregationstatus) {
  ```
- `public/lib/grade/grade_grade.php:405`
  ```php
  protected function get_grade_min_and_max() {
  ```
- `public/lib/grade/grade_grade.php:440`
  ```php
  public function get_grade_min() {
  ```
- `public/lib/grade/grade_grade.php:452`
  ```php
  public function get_grade_max() {
  ```
- `public/lib/grade/grade_grade.php:463`
  ```php
  public function get_dategraded() {
  ```
- `public/lib/grade/grade_grade.php:481`
  ```php
  public function set_overridden($state, $refresh = true) {
  ```
- `public/lib/grade/grade_grade.php:506`
  ```php
  public function is_excluded() {
  ```
- `public/lib/grade/grade_grade.php:516`
  ```php
  public function set_excluded($state) {
  ```
- `public/lib/grade/grade_grade.php:538`
  ```php
  public function set_locked($lockedstate, $cascade=false, $refresh=true) {
  ```
- `public/lib/grade/grade_grade.php:577`
  ```php
  public static function check_locktime_all($items) {
  ```
- `public/lib/grade/grade_grade.php:598`
  ```php
  public function set_locktime($locktime) {
  ```
- `public/lib/grade/grade_grade.php:608`
  ```php
  public function get_locktime() {
  ```
- `public/lib/grade/grade_grade.php:626`
  ```php
  public function is_hidden() {
  ```
- `public/lib/grade/grade_grade.php:640`
  ```php
  public function is_hiddenuntil() {
  ```
- `public/lib/grade/grade_grade.php:659`
  ```php
  public function get_hidden() {
  ```
- `public/lib/grade/grade_grade.php:689`
  ```php
  public function set_hidden($hidden, $cascade=false) {
  ```
- `public/lib/grade/grade_grade.php:700`
  ```php
  public static function fetch($params) {
  ```
- `public/lib/grade/grade_grade.php:710`
  ```php
  public static function fetch_all($params) {
  ```
- `public/lib/grade/grade_grade.php:726`
  ```php
  public static function standardise_score($rawgrade, $source_min, $source_max, $target_min, $target_max) {
  ```
- `public/lib/grade/grade_grade.php:757`
  ```php
  protected static function flatten_dependencies_array(&$dependson, &$dependencydepth) {
  ```
- `public/lib/grade/grade_grade.php:806`
  ```php
  public static function get_hiding_affected(&$grade_grades, &$grade_items) {
  ```
- `public/lib/grade/grade_item.php:1037`
  ```php
  public function force_regrading() {
  ```
- `public/lib/grade/grade_item.php:1051`
  ```php
  public function load_scale() {
  ```
- `public/lib/grade/grade_item.php:1085`
  ```php
  public function load_outcome() {
  ```
- `public/lib/grade/grade_item.php:1098`
  ```php
  public function get_parent_category() {
  ```
- `public/lib/grade/grade_item.php:1113`
  ```php
  public function load_parent_category() {
  ```
- `public/lib/grade/grade_item.php:1125`
  ```php
  public function get_item_category() {
  ```
- `public/lib/grade/grade_item.php:1138`
  ```php
  public function load_item_category() {
  ```
- `public/lib/grade/grade_item.php:1150`
  ```php
  public function is_category_item() {
  ```
- `public/lib/grade/grade_item.php:1159`
  ```php
  public function is_course_item() {
  ```
- `public/lib/grade/grade_item.php:1168`
  ```php
  public function is_manual_item() {
  ```
- `public/lib/grade/grade_item.php:1177`
  ```php
  public function is_outcome_item() {
  ```
- `public/lib/grade/grade_item.php:1186`
  ```php
  public function is_external_item() {
  ```
- `public/lib/grade/grade_item.php:1195`
  ```php
  public function is_overridable_item() {
  ```
- `public/lib/grade/grade_item.php:1210`
  ```php
  public function is_overridable_item_feedback() {
  ```
- `public/lib/grade/grade_item.php:1219`
  ```php
  public function is_raw_used() {
  ```
- `public/lib/grade/grade_item.php:1229`
  ```php
  public function is_aggregate_item() {
  ```
- `public/lib/grade/grade_item.php:1240`
  ```php
  public function is_gradable(): bool {
  ```
- `public/lib/grade/grade_item.php:1250`
  ```php
  public static function fetch_course_item($courseid) {
  ```
- `public/lib/grade/grade_item.php:1265`
  ```php
  public function is_editable() {
  ```
- `public/lib/grade/grade_item.php:1274`
  ```php
  public function is_calculated() {
  ```
- `public/lib/grade/grade_item.php:1298`
  ```php
  public function get_calculation() {
  ```
- `public/lib/grade/grade_item.php:1315`
  ```php
  public function set_calculation($formula) {
  ```
- `public/lib/grade/grade_item.php:1328`
  ```php
  public static function denormalize_formula($formula, $courseid) {
  ```
- `public/lib/grade/grade_item.php:1355`
  ```php
  public static function normalize_formula($formula, $courseid) {
  ```
- `public/lib/grade/grade_item.php:1379`
  ```php
  public function get_final($userid=NULL) {
  ```
- `public/lib/grade/grade_item.php:1407`
  ```php
  public function get_grade($userid, $create=true) {
  ```
- `public/lib/grade/grade_item.php:1427`
  ```php
  public function get_sortorder() {
  ```
- `public/lib/grade/grade_item.php:1437`
  ```php
  public function get_idnumber() {
  ```
- `public/lib/grade/grade_item.php:1447`
  ```php
  public function get_grade_item() {
  ```
- `public/lib/grade/grade_item.php:1457`
  ```php
  public function set_sortorder($sortorder) {
  ```
- `public/lib/grade/grade_item.php:1470`
  ```php
  public function move_after_sortorder($sortorder) {
  ```
- `public/lib/grade/grade_item.php:1490`
  ```php
  public static function fix_duplicate_sortorder($courseid) {
  ```
- `public/lib/grade/grade_item.php:1528`
  ```php
  public function get_name($fulltotal=false, $escape = true) {
  ```
- `public/lib/grade/grade_item.php:1566`
  ```php
  public function get_description() {
  ```
- `public/lib/grade/grade_item.php:1583`
  ```php
  public function set_parent($parentid, $updateaggregationfields = true) {
  ```
- `public/lib/grade/grade_item.php:1628`
  ```php
  public function set_aggregation_fields_for_aggregation($from, $to) {
  ```
- `public/lib/grade/grade_item.php:1673`
  ```php
  public function bounded_grade($gradevalue) {
  ```
- `public/lib/grade/grade_item.php:1711`
  ```php
  public function depends_on($reset_cache=false) {
  ```
- `public/lib/grade/grade_item.php:1807`
  ```php
  public function refresh_grades($userid=0) {
  ```
- `public/lib/grade/grade_item.php:1850`
  ```php
  public function update_final_grade($userid, $finalgrade = false, $source = null, $feedback = false,
  ```
- `public/lib/grade/grade_item.php:2008`
  ```php
  public function update_raw_grade($userid, $rawgrade = false, $source = null, $feedback = false,
  ```
- `public/lib/grade/grade_item.php:2187`
  ```php
  public function update_deducted_mark(int $userid, float $deductedmark): void {
  ```
- `public/lib/grade/grade_item.php:2203`
  ```php
  public function compute($userid=null) {
  ```
- `public/lib/grade/grade_item.php:2312`
  ```php
  public function use_formula($userid, $params, $useditems, $oldgrade) {
  ```
- `public/lib/grade/grade_item.php:2441`
  ```php
  public function validate_formula($formulastr) {
  ```
- `public/lib/grade/grade_item.php:2521`
  ```php
  public function get_displaytype() {
  ```
- `public/lib/grade/grade_item.php:2539`
  ```php
  public function get_decimals() {
  ```
- `public/lib/grade/grade_item.php:2557`
  ```php
  function get_formatted_range($rangesdisplaytype=null, $rangesdecimalpoints=null) {
  ```
- `public/lib/grade/grade_item.php:2597`
  ```php
  public function get_coefstring() {
  ```
- `public/lib/grade/grade_item.php:2615`
  ```php
  public function can_control_visibility() {
  ```
- `public/lib/grade/grade_item.php:2628`
  ```php
  protected function notify_changed($deleted) {
  ```
- `public/lib/grade/grade_item.php:2644`
  ```php
  public function get_context() {
  ```
- `public/lib/grade/grade_item.php:288`
  ```php
  public function __construct($params = null, $fetch = true) {
  ```
- `public/lib/grade/grade_item.php:304`
  ```php
  public function update($source = null, $isbulkupdate = false) {
  ```
- `public/lib/grade/grade_item.php:346`
  ```php
  public function qualifies_for_regrading() {
  ```
- `public/lib/grade/grade_item.php:382`
  ```php
  public static function fetch($params) {
  ```
- `public/lib/grade/grade_item.php:391`
  ```php
  public function has_grades() {
  ```
- `public/lib/grade/grade_item.php:405`
  ```php
  public function has_overridden_grades() {
  ```
- `public/lib/grade/grade_item.php:421`
  ```php
  public static function fetch_all($params) {
  ```
- `public/lib/grade/grade_item.php:431`
  ```php
  public function delete($source=null) {
  ```
- `public/lib/grade/grade_item.php:455`
  ```php
  public function delete_all_grades($source=null) {
  ```
- `public/lib/grade/grade_item.php:490`
  ```php
  public function duplicate() {
  ```
- `public/lib/grade/grade_item.php:527`
  ```php
  public function insert($source = null, $isbulkupdate = false) {
  ```
- `public/lib/grade/grade_item.php:587`
  ```php
  public function add_idnumber($idnumber) {
  ```
- `public/lib/grade/grade_item.php:624`
  ```php
  public function is_locked($userid=NULL) {
  ```
- `public/lib/grade/grade_item.php:655`
  ```php
  public function set_locked($lockedstate, $cascade=false, $refresh=true) {
  ```
- `public/lib/grade/grade_item.php:711`
  ```php
  public function check_locktime() {
  ```
- `public/lib/grade/grade_item.php:728`
  ```php
  public function set_locktime($locktime) {
  ```
- `public/lib/grade/grade_item.php:738`
  ```php
  public function get_locktime() {
  ```
- `public/lib/grade/grade_item.php:750`
  ```php
  public function set_hidden($hidden, $cascade=false) {
  ```
- `public/lib/grade/grade_item.php:781`
  ```php
  public function has_hidden_grades($groupsql="", ?array $params=null, $groupwheresql="") {
  ```
- `public/lib/grade/grade_item.php:794`
  ```php
  public function regrading_finished() {
  ```
- `public/lib/grade/grade_item.php:812`
  ```php
  public function regrade_final_grades($userid=null, ?\core\progress\base $progress = null) {
  ```
- `public/lib/grade/grade_item.php:914`
  ```php
  public function adjust_raw_grade($rawgrade, $rawmin, $rawmax) {
  ```
- `public/lib/grade/grade_item.php:988`
  ```php
  public function rescale_grades_keep_percentage($oldgrademin, $oldgrademax, $newgrademin, $newgrademax, $source = null) {
  ```
- `public/lib/grade/grade_object.php:112`
  ```php
  public function load_optional_fields() {
  ```
- `public/lib/grade/grade_object.php:134`
  ```php
  public static function fetch($params) {
  ```
- `public/lib/grade/grade_object.php:147`
  ```php
  public static function fetch_all($params) {
  ```
- `public/lib/grade/grade_object.php:159`
  ```php
  protected static function fetch_helper($table, $classname, $params) {
  ```
- `public/lib/grade/grade_object.php:180`
  ```php
  public static function fetch_all_helper($table, $classname, $params, string $sortby = 'id ASC') {
  ```
- `public/lib/grade/grade_object.php:245`
  ```php
  public function update($source = null, $isbulkupdate = false) {
  ```
- `public/lib/grade/grade_object.php:281`
  ```php
  public function delete($source=null) {
  ```
- `public/lib/grade/grade_object.php:318`
  ```php
  public function get_record_data() {
  ```
- `public/lib/grade/grade_object.php:342`
  ```php
  public function insert($source = null, $isbulkupdate = false) {
  ```
- `public/lib/grade/grade_object.php:385`
  ```php
  public function update_from_db() {
  ```
- `public/lib/grade/grade_object.php:409`
  ```php
  public static function set_properties(&$instance, $params) {
  ```
- `public/lib/grade/grade_object.php:425`
  ```php
  protected function notify_changed($deleted) {
  ```
- `public/lib/grade/grade_object.php:433`
  ```php
  protected function add_feedback_files(?int $historyid = null) {
  ```
- `public/lib/grade/grade_object.php:441`
  ```php
  protected function update_feedback_files(?int $historyid = null) {
  ```
- `public/lib/grade/grade_object.php:447`
  ```php
  protected function delete_feedback_files() {
  ```
- `public/lib/grade/grade_object.php:457`
  ```php
  function is_hidden() {
  ```
- `public/lib/grade/grade_object.php:466`
  ```php
  function is_hiddenuntil() {
  ```
- `public/lib/grade/grade_object.php:475`
  ```php
  function get_hidden() {
  ```
- `public/lib/grade/grade_object.php:485`
  ```php
  function set_hidden($hidden, $cascade=false) {
  ```
- `public/lib/grade/grade_object.php:495`
  ```php
  public function can_control_visibility() {
  ```
- `public/lib/grade/grade_object.php:87`
  ```php
  public function __construct($params=NULL, $fetch=true) {
  ```
- `public/lib/grade/grade_outcome.php:106`
  ```php
  public function delete($source=null) {
  ```
- `public/lib/grade/grade_outcome.php:132`
  ```php
  public function insert($source = null, $isbulkupdate = false) {
  ```
- `public/lib/grade/grade_outcome.php:155`
  ```php
  public function update($source = null, $isbulkupdate = false) {
  ```
- `public/lib/grade/grade_outcome.php:172`
  ```php
  public function use_in($courseid) {
  ```
- `public/lib/grade/grade_outcome.php:194`
  ```php
  public static function fetch($params) {
  ```
- `public/lib/grade/grade_outcome.php:205`
  ```php
  public static function fetch_all($params) {
  ```
- `public/lib/grade/grade_outcome.php:214`
  ```php
  public function load_scale() {
  ```
- `public/lib/grade/grade_outcome.php:228`
  ```php
  public static function fetch_all_global() {
  ```
- `public/lib/grade/grade_outcome.php:242`
  ```php
  public static function fetch_all_local($courseid) {
  ```
- `public/lib/grade/grade_outcome.php:256`
  ```php
  public static function fetch_all_available($courseid) {
  ```
- `public/lib/grade/grade_outcome.php:283`
  ```php
  public function get_name() {
  ```
- `public/lib/grade/grade_outcome.php:294`
  ```php
  public function get_shortname() {
  ```
- `public/lib/grade/grade_outcome.php:303`
  ```php
  public function get_description() {
  ```
- `public/lib/grade/grade_outcome.php:319`
  ```php
  public function can_delete() {
  ```
- `public/lib/grade/grade_outcome.php:336`
  ```php
  public function get_course_uses_count() {
  ```
- `public/lib/grade/grade_outcome.php:351`
  ```php
  public function get_item_uses_count() {
  ```
- `public/lib/grade/grade_outcome.php:369`
  ```php
  public function get_grade_info($courseid=null, $average=true, $items=false) {
  ```
- `public/lib/grade/grade_scale.php:108`
  ```php
  public static function fetch($params) {
  ```
- `public/lib/grade/grade_scale.php:119`
  ```php
  public static function fetch_all($params) {
  ```
- `public/lib/grade/grade_scale.php:132`
  ```php
  public function insert($source = null, $isbulkupdate = false) {
  ```
- `public/lib/grade/grade_scale.php:164`
  ```php
  public function update($source = null, $isbulkupdate = false) {
  ```
- `public/lib/grade/grade_scale.php:194`
  ```php
  public function delete($source=null) {
  ```
- `public/lib/grade/grade_scale.php:230`
  ```php
  public function get_name() {
  ```
- `public/lib/grade/grade_scale.php:246`
  ```php
  public function load_items($items=NULL) {
  ```
- `public/lib/grade/grade_scale.php:276`
  ```php
  public function compact_items($items=NULL) {
  ```
- `public/lib/grade/grade_scale.php:297`
  ```php
  public function get_nearest_item($grade) {
  ```
- `public/lib/grade/grade_scale.php:317`
  ```php
  public static function fetch_all_global() {
  ```
- `public/lib/grade/grade_scale.php:327`
  ```php
  public static function fetch_all_local($courseid) {
  ```
- `public/lib/grade/grade_scale.php:336`
  ```php
  public function is_last_global_scale() {
  ```
- `public/lib/grade/grade_scale.php:345`
  ```php
  public function can_delete() {
  ```
- `public/lib/grade/grade_scale.php:354`
  ```php
  public function is_used() {
  ```
- `public/lib/grade/grade_scale.php:394`
  ```php
  public function get_description() {
  ```
- `public/lib/grade/tests/fixtures/lib.php:1009`
  ```php
  public static function test_flatten_dependencies_array(&$a,&$b) {
  ```
- `public/lib/grade/tests/fixtures/lib.php:117`
  ```php
  private function load_scales() {
  ```
- `public/lib/grade/tests/fixtures/lib.php:186`
  ```php
  private function load_grade_categories() {
  ```
- `public/lib/grade/tests/fixtures/lib.php:323`
  ```php
  protected function load_grade_items() {
  ```
- `public/lib/grade/tests/fixtures/lib.php:58`
  ```php
  protected function setUp(): void {
  ```
- `public/lib/grade/tests/fixtures/lib.php:709`
  ```php
  private function load_grade_grades() {
  ```
- `public/lib/grade/tests/fixtures/lib.php:88`
  ```php
  private function load_modules() {
  ```
- `public/lib/grade/tests/fixtures/lib.php:939`
  ```php
  private function load_grade_outcomes() {
  ```
- `public/lib/grade/tests/grade_category_test.php:122`
  ```php
  protected function sub_test_grade_category_build_path() {
  ```
- `public/lib/grade/tests/grade_category_test.php:129`
  ```php
  protected function sub_test_grade_category_fetch() {
  ```
- `public/lib/grade/tests/grade_category_test.php:138`
  ```php
  protected function sub_test_grade_category_fetch_all() {
  ```
- `public/lib/grade/tests/grade_category_test.php:146`
  ```php
  protected function sub_test_grade_category_update() {
  ```
- `public/lib/grade/tests/grade_category_test.php:174`
  ```php
  protected function sub_test_grade_category_delete() {
  ```
- `public/lib/grade/tests/grade_category_test.php:184`
  ```php
  protected function sub_test_grade_category_insert() {
  ```
- `public/lib/grade/tests/grade_category_test.php:215`
  ```php
  protected function sub_test_grade_category_qualifies_for_regrading() {
  ```
- `public/lib/grade/tests/grade_category_test.php:232`
  ```php
  protected function sub_test_grade_category_force_regrading() {
  ```
- `public/lib/grade/tests/grade_category_test.php:253`
  ```php
  protected function sub_test_grade_category_generate_grades_aggregationweight() {
  ```
- `public/lib/grade/tests/grade_category_test.php:313`
  ```php
  protected function sub_test_grade_category_generate_grades() {
  ```
- `public/lib/grade/tests/grade_category_test.php:34`
  ```php
  public function test_grade_category(): void {
  ```
- `public/lib/grade/tests/grade_category_test.php:421`
  ```php
  protected function helper_test_grade_agg_method($grade_category, $grade_items, $grade_grades, $aggmethod, $aggmethodname, $correct1, $correct2) {
  ```
- `public/lib/grade/tests/grade_category_test.php:441`
  ```php
  protected function helper_test_grade_aggregation_result($grade_category, $correctgrade, $msg) {
  ```
- `public/lib/grade/tests/grade_category_test.php:467`
  ```php
  protected function sub_test_grade_category_aggregate_grades() {
  ```
- `public/lib/grade/tests/grade_category_test.php:473`
  ```php
  protected function sub_test_grade_category_apply_limit_rules() {
  ```
- `public/lib/grade/tests/grade_category_test.php:605`
  ```php
  protected function sub_test_grade_category_is_aggregationcoef_used() {
  ```
- `public/lib/grade/tests/grade_category_test.php:630`
  ```php
  protected function sub_test_grade_category_aggregation_uses_aggregationcoef() {
  ```
- `public/lib/grade/tests/grade_category_test.php:644`
  ```php
  protected function sub_test_grade_category_fetch_course_tree() {
  ```
- `public/lib/grade/tests/grade_category_test.php:650`
  ```php
  protected function sub_test_grade_category_get_children() {
  ```
- `public/lib/grade/tests/grade_category_test.php:669`
  ```php
  protected function sub_test_grade_category_load_grade_item() {
  ```
- `public/lib/grade/tests/grade_category_test.php:677`
  ```php
  protected function sub_test_grade_category_get_grade_item() {
  ```
- `public/lib/grade/tests/grade_category_test.php:684`
  ```php
  protected function sub_test_grade_category_load_parent_category() {
  ```
- `public/lib/grade/tests/grade_category_test.php:692`
  ```php
  protected function sub_test_grade_category_get_parent_category() {
  ```
- `public/lib/grade/tests/grade_category_test.php:702`
  ```php
  protected function sub_test_grade_category_get_name_escaped() {
  ```
- `public/lib/grade/tests/grade_category_test.php:712`
  ```php
  protected function sub_test_grade_category_get_name_unescaped() {
  ```
- `public/lib/grade/tests/grade_category_test.php:719`
  ```php
  protected function sub_test_grade_category_set_parent() {
  ```
- `public/lib/grade/tests/grade_category_test.php:729`
  ```php
  protected function sub_test_grade_category_get_final() {
  ```
- `public/lib/grade/tests/grade_category_test.php:736`
  ```php
  protected function sub_test_grade_category_get_sortorder() {
  ```
- `public/lib/grade/tests/grade_category_test.php:743`
  ```php
  protected function sub_test_grade_category_set_sortorder() {
  ```
- `public/lib/grade/tests/grade_category_test.php:750`
  ```php
  protected function sub_test_grade_category_move_after_sortorder() {
  ```
- `public/lib/grade/tests/grade_category_test.php:757`
  ```php
  protected function sub_test_grade_category_is_course_category() {
  ```
- `public/lib/grade/tests/grade_category_test.php:763`
  ```php
  protected function sub_test_grade_category_fetch_course_category() {
  ```
- `public/lib/grade/tests/grade_category_test.php:772`
  ```php
  protected function sub_test_grade_category_is_editable() {
  ```
- `public/lib/grade/tests/grade_category_test.php:776`
  ```php
  protected function sub_test_grade_category_is_locked() {
  ```
- `public/lib/grade/tests/grade_category_test.php:783`
  ```php
  protected function sub_test_grade_category_set_locked() {
  ```
- `public/lib/grade/tests/grade_category_test.php:800`
  ```php
  protected function sub_test_grade_category_is_hidden() {
  ```
- `public/lib/grade/tests/grade_category_test.php:807`
  ```php
  protected function sub_test_grade_category_set_hidden() {
  ```
- `public/lib/grade/tests/grade_category_test.php:815`
  ```php
  protected function sub_test_grade_category_can_control_visibility() {
  ```
- `public/lib/grade/tests/grade_category_test.php:820`
  ```php
  protected function sub_test_grade_category_insert_course_category() {
  ```
- `public/lib/grade/tests/grade_category_test.php:82`
  ```php
  protected function sub_test_grade_category_construct() {
  ```
- `public/lib/grade/tests/grade_category_test.php:834`
  ```php
  protected function generate_random_raw_grade($item, $userid) {
  ```
- `public/lib/grade/tests/grade_category_test.php:846`
  ```php
  protected function sub_test_grade_category_is_extracredit_used() {
  ```
- `public/lib/grade/tests/grade_category_test.php:871`
  ```php
  protected function sub_test_grade_category_aggregation_uses_extracredit() {
  ```
- `public/lib/grade/tests/grade_category_test.php:888`
  ```php
  protected function sub_test_grade_category_total_visibility() {
  ```
- `public/lib/grade/tests/grade_grade_test.php:105`
  ```php
  protected function sub_test_grade_grade_fetch_all() {
  ```
- `public/lib/grade/tests/grade_grade_test.php:113`
  ```php
  protected function sub_test_grade_grade_load_grade_item() {
  ```
- `public/lib/grade/tests/grade_grade_test.php:124`
  ```php
  protected function sub_test_grade_grade_standardise_score() {
  ```
- `public/lib/grade/tests/grade_grade_test.php:134`
  ```php
  public function test_grade_grade_set_locked(): void {
  ```
- `public/lib/grade/tests/grade_grade_test.php:162`
  ```php
  protected function sub_test_grade_grade_is_locked() {
  ```
- `public/lib/grade/tests/grade_grade_test.php:171`
  ```php
  protected function sub_test_grade_grade_set_hidden() {
  ```
- `public/lib/grade/tests/grade_grade_test.php:189`
  ```php
  protected function sub_test_grade_grade_is_hidden() {
  ```
- `public/lib/grade/tests/grade_grade_test.php:209`
  ```php
  public function test_flatten_dependencies(): void {
  ```
- `public/lib/grade/tests/grade_grade_test.php:306`
  ```php
  public function test_grade_grade_min_max(): void {
  ```
- `public/lib/grade/tests/grade_grade_test.php:33`
  ```php
  public function test_grade_grade(): void {
  ```
- `public/lib/grade/tests/grade_grade_test.php:375`
  ```php
  public function test_grade_grade_min_max_with_course_item(): void {
  ```
- `public/lib/grade/tests/grade_grade_test.php:435`
  ```php
  public function test_grade_grade_min_max_with_category_item(): void {
  ```
- `public/lib/grade/tests/grade_grade_test.php:48`
  ```php
  protected function sub_test_grade_grade_construct() {
  ```
- `public/lib/grade/tests/grade_grade_test.php:508`
  ```php
  public function sub_test_grade_grade_deleted() {
  ```
- `public/lib/grade/tests/grade_grade_test.php:567`
  ```php
  private function add_feedback_file_to_copy() {
  ```
- `public/lib/grade/tests/grade_grade_test.php:584`
  ```php
  public function sub_test_grade_grade_deleted_event() {
  ```
- `public/lib/grade/tests/grade_grade_test.php:62`
  ```php
  protected function sub_test_grade_grade_insert() {
  ```
- `public/lib/grade/tests/grade_grade_test.php:647`
  ```php
  public function test_category_get_hiding_affected(): void {
  ```
- `public/lib/grade/tests/grade_grade_test.php:715`
  ```php
  private function call_get_hiding_affected($course, $user) {
  ```
- `public/lib/grade/tests/grade_grade_test.php:91`
  ```php
  protected function sub_test_grade_grade_update() {
  ```
- `public/lib/grade/tests/grade_grade_test.php:96`
  ```php
  protected function sub_test_grade_grade_fetch() {
  ```
- `public/lib/grade/tests/grade_item_test.php:1096`
  ```php
  protected function sub_test_grade_item_created_event() {
  ```
- `public/lib/grade/tests/grade_item_test.php:1131`
  ```php
  protected function sub_test_grade_item_updated_event() {
  ```
- `public/lib/grade/tests/grade_item_test.php:1169`
  ```php
  public function test_grade_duplicate_grade_item_success(): void {
  ```
- `public/lib/grade/tests/grade_item_test.php:118`
  ```php
  protected function sub_test_grade_item_delete() {
  ```
- `public/lib/grade/tests/grade_item_test.php:1209`
  ```php
  public function test_grade_duplicate_grade_item_incomplete(): void {
  ```
- `public/lib/grade/tests/grade_item_test.php:1220`
  ```php
  public function test_grade_duplicate_grade_item_not_in_db(): void {
  ```
- `public/lib/grade/tests/grade_item_test.php:159`
  ```php
  protected function sub_test_grade_item_delete_disabled_modules(): void {
  ```
- `public/lib/grade/tests/grade_item_test.php:210`
  ```php
  protected function sub_test_grade_item_update() {
  ```
- `public/lib/grade/tests/grade_item_test.php:227`
  ```php
  protected function sub_test_grade_item_load_scale() {
  ```
- `public/lib/grade/tests/grade_item_test.php:235`
  ```php
  protected function sub_test_grade_item_load_outcome() {
  ```
- `public/lib/grade/tests/grade_item_test.php:241`
  ```php
  protected function sub_test_grade_item_qualifies_for_regrading() {
  ```
- `public/lib/grade/tests/grade_item_test.php:256`
  ```php
  protected function sub_test_grade_item_force_regrading() {
  ```
- `public/lib/grade/tests/grade_item_test.php:268`
  ```php
  protected function sub_test_grade_item_fetch() {
  ```
- `public/lib/grade/tests/grade_item_test.php:282`
  ```php
  protected function sub_test_grade_item_fetch_all() {
  ```
- `public/lib/grade/tests/grade_item_test.php:291`
  ```php
  protected function sub_test_grade_item_get_all_finals() {
  ```
- `public/lib/grade/tests/grade_item_test.php:301`
  ```php
  protected function sub_test_grade_item_get_final() {
  ```
- `public/lib/grade/tests/grade_item_test.php:308`
  ```php
  protected function sub_test_grade_item_get_sortorder() {
  ```
- `public/lib/grade/tests/grade_item_test.php:315`
  ```php
  protected function sub_test_grade_item_set_sortorder() {
  ```
- `public/lib/grade/tests/grade_item_test.php:322`
  ```php
  protected function sub_test_grade_item_move_after_sortorder() {
  ```
- `public/lib/grade/tests/grade_item_test.php:32`
  ```php
  public function test_grade_item(): void {
  ```
- `public/lib/grade/tests/grade_item_test.php:338`
  ```php
  protected function sub_test_grade_item_get_name_escaped() {
  ```
- `public/lib/grade/tests/grade_item_test.php:348`
  ```php
  protected function sub_test_grade_item_get_name_unescaped() {
  ```
- `public/lib/grade/tests/grade_item_test.php:355`
  ```php
  protected function sub_test_grade_item_set_parent() {
  ```
- `public/lib/grade/tests/grade_item_test.php:371`
  ```php
  protected function sub_test_grade_item_get_parent_category() {
  ```
- `public/lib/grade/tests/grade_item_test.php:379`
  ```php
  protected function sub_test_grade_item_load_parent_category() {
  ```
- `public/lib/grade/tests/grade_item_test.php:388`
  ```php
  protected function sub_test_grade_item_get_item_category() {
  ```
- `public/lib/grade/tests/grade_item_test.php:396`
  ```php
  protected function sub_test_grade_item_load_item_category() {
  ```
- `public/lib/grade/tests/grade_item_test.php:405`
  ```php
  protected function sub_test_grade_item_regrade_final_grades() {
  ```
- `public/lib/grade/tests/grade_item_test.php:412`
  ```php
  protected function sub_test_grade_item_adjust_raw_grade() {
  ```
- `public/lib/grade/tests/grade_item_test.php:473`
  ```php
  protected function sub_test_grade_item_rescale_grades_keep_percentage() {
  ```
- `public/lib/grade/tests/grade_item_test.php:513`
  ```php
  protected function sub_test_grade_item_set_locked() {
  ```
- `public/lib/grade/tests/grade_item_test.php:538`
  ```php
  protected function sub_test_grade_item_is_locked() {
  ```
- `public/lib/grade/tests/grade_item_test.php:549`
  ```php
  protected function sub_test_grade_item_set_hidden() {
  ```
- `public/lib/grade/tests/grade_item_test.php:564`
  ```php
  protected function sub_test_grade_item_is_hidden() {
  ```
- `public/lib/grade/tests/grade_item_test.php:592`
  ```php
  public function sub_test_grade_item_is_gradable(int $type, bool $expected): void {
  ```
- `public/lib/grade/tests/grade_item_test.php:603`
  ```php
  public static function provider_itemtype_is_gradable(): \Generator {
  ```
- `public/lib/grade/tests/grade_item_test.php:622`
  ```php
  protected function sub_test_grade_item_is_category_item() {
  ```
- `public/lib/grade/tests/grade_item_test.php:628`
  ```php
  protected function sub_test_grade_item_is_course_item() {
  ```
- `public/lib/grade/tests/grade_item_test.php:634`
  ```php
  protected function sub_test_grade_item_fetch_course_item() {
  ```
- `public/lib/grade/tests/grade_item_test.php:640`
  ```php
  protected function sub_test_grade_item_depends_on() {
  ```
- `public/lib/grade/tests/grade_item_test.php:667`
  ```php
  protected function scales_outcomes_test_grade_item_depends_on() {
  ```
- `public/lib/grade/tests/grade_item_test.php:699`
  ```php
  protected function sub_test_refresh_grades() {
  ```
- `public/lib/grade/tests/grade_item_test.php:711`
  ```php
  protected function sub_test_grade_item_is_calculated() {
  ```
- `public/lib/grade/tests/grade_item_test.php:720`
  ```php
  protected function sub_test_grade_item_set_calculation() {
  ```
- `public/lib/grade/tests/grade_item_test.php:731`
  ```php
  protected function sub_test_grade_item_get_calculation() {
  ```
- `public/lib/grade/tests/grade_item_test.php:743`
  ```php
  public function sub_test_grade_item_compute() {
  ```
- `public/lib/grade/tests/grade_item_test.php:773`
  ```php
  protected function sub_test_update_final_grade() {
  ```
- `public/lib/grade/tests/grade_item_test.php:79`
  ```php
  protected function sub_test_grade_item_construct() {
  ```
- `public/lib/grade/tests/grade_item_test.php:804`
  ```php
  protected function sub_test_grade_item_can_control_visibility() {
  ```
- `public/lib/grade/tests/grade_item_test.php:818`
  ```php
  public function sub_test_grade_item_fix_sortorder() {
  ```
- `public/lib/grade/tests/grade_item_test.php:896`
  ```php
  private function insert_fake_grade_item_sortorder($courseid, $sortorder) {
  ```
- `public/lib/grade/tests/grade_item_test.php:916`
  ```php
  public function test_set_aggregation_fields_for_aggregation(): void {
  ```
- `public/lib/grade/tests/grade_item_test.php:96`
  ```php
  protected function sub_test_grade_item_insert() {
  ```
- `public/lib/grade/tests/grade_object_test.php:33`
  ```php
  public function test_fetch_all_helper(): void {
  ```
- `public/lib/grade/tests/grade_outcome_test.php:101`
  ```php
  protected function sub_test_grade_outcome_fetch_all() {
  ```
- `public/lib/grade/tests/grade_outcome_test.php:33`
  ```php
  public function test_grade_outcome(): void {
  ```
- `public/lib/grade/tests/grade_outcome_test.php:42`
  ```php
  protected function sub_test_grade_outcome_construct() {
  ```
- `public/lib/grade/tests/grade_outcome_test.php:53`
  ```php
  protected function sub_test_grade_outcome_insert() {
  ```
- `public/lib/grade/tests/grade_outcome_test.php:70`
  ```php
  protected function sub_test_grade_outcome_update() {
  ```
- `public/lib/grade/tests/grade_outcome_test.php:80`
  ```php
  protected function sub_test_grade_outcome_delete() {
  ```
- `public/lib/grade/tests/grade_outcome_test.php:89`
  ```php
  protected function sub_test_grade_outcome_fetch() {
  ```
- `public/lib/grade/tests/grade_scale_test.php:103`
  ```php
  protected function sub_test_grade_scale_fetch() {
  ```
- `public/lib/grade/tests/grade_scale_test.php:112`
  ```php
  protected function sub_test_scale_load_items() {
  ```
- `public/lib/grade/tests/grade_scale_test.php:122`
  ```php
  protected function sub_test_scale_compact_items() {
  ```
- `public/lib/grade/tests/grade_scale_test.php:134`
  ```php
  protected function sub_test_scale_one_item() {
  ```
- `public/lib/grade/tests/grade_scale_test.php:33`
  ```php
  public function test_grade_scale(): void {
  ```
- `public/lib/grade/tests/grade_scale_test.php:44`
  ```php
  protected function sub_test_scale_construct() {
  ```
- `public/lib/grade/tests/grade_scale_test.php:61`
  ```php
  protected function sub_test_grade_scale_insert() {
  ```
- `public/lib/grade/tests/grade_scale_test.php:80`
  ```php
  protected function sub_test_grade_scale_update() {
  ```
- `public/lib/grade/tests/grade_scale_test.php:91`
  ```php
  protected function sub_test_grade_scale_delete() {
  ```
- `public/lib/gradelib.php:1025`
  ```php
  function grade_verify_idnumber($idnumber, $courseid, $grade_item=null, $cm=null) {
  ```
- `public/lib/gradelib.php:1061`
  ```php
  function grade_force_full_regrading($courseid) {
  ```
- `public/lib/gradelib.php:1069`
  ```php
  function grade_force_site_regrading() {
  ```
- `public/lib/gradelib.php:1080`
  ```php
  function grade_recover_history_grades($userid, $courseid) {
  ```
- `public/lib/gradelib.php:1162`
  ```php
  function grade_regrade_final_grades($courseid, $userid=null, $updated_item=null, $progress=null, bool $async = false) {
  ```
- `public/lib/gradelib.php:1390`
  ```php
  function grade_grab_course_grades($courseid, $modname=null, $userid=0) {
  ```
- `public/lib/gradelib.php:1440`
  ```php
  function grade_update_mod_grades($modinstance, $userid=0) {
  ```
- `public/lib/gradelib.php:1472`
  ```php
  function remove_grade_letters($context, $showfeedback) {
  ```
- `public/lib/gradelib.php:1502`
  ```php
  function remove_course_grades($courseid, $showfeedback) {
  ```
- `public/lib/gradelib.php:1548`
  ```php
  function grade_course_category_delete($categoryid, $newparentid, $showfeedback) {
  ```
- `public/lib/gradelib.php:1570`
  ```php
  function grade_uninstalled_module($modname) {
  ```
- `public/lib/gradelib.php:1591`
  ```php
  function grade_user_delete($userid) {
  ```
- `public/lib/gradelib.php:1605`
  ```php
  function grade_user_unenrol($courseid, $userid) {
  ```
- `public/lib/gradelib.php:1623`
  ```php
  function grade_course_reset($courseid) {
  ```
- `public/lib/gradelib.php:1648`
  ```php
  function grade_floatval(?float $number) {
  ```
- `public/lib/gradelib.php:1665`
  ```php
  function grade_floats_different(?float $f1, ?float $f2): bool {
  ```
- `public/lib/gradelib.php:1681`
  ```php
  function grade_floats_equal(?float $f1, ?float $f2): bool {
  ```
- `public/lib/gradelib.php:1692`
  ```php
  function grade_get_date_for_user_grade(\stdClass $grade, \stdClass $user): ?int {
  ```
- `public/lib/gradelib.php:320`
  ```php
  function is_gradable(int $courseid, string $itemtype, string $itemmodule, int $iteminstance): bool {
  ```
- `public/lib/gradelib.php:350`
  ```php
  function grade_update_outcomes($source, $courseid, $itemtype, $itemmodule, $iteminstance, $userid, $data) {
  ```
- `public/lib/gradelib.php:371`
  ```php
  function grade_needs_regrade_final_grades($courseid) {
  ```
- `public/lib/gradelib.php:383`
  ```php
  function grade_needs_regrade_progress_bar($courseid) {
  ```
- `public/lib/gradelib.php:414`
  ```php
  function grade_regrade_final_grades_if_required($course, ?callable $callback = null) {
  ```
- `public/lib/gradelib.php:452`
  ```php
  function grade_get_grades($courseid, $itemtype, $itemmodule, $iteminstance, $userid_or_ids=null) {
  ```
- `public/lib/gradelib.php:64`
  ```php
  function grade_update($source, $courseid, $itemtype, $itemmodule, $iteminstance, $itemnumber, $grades = null,
  ```
- `public/lib/gradelib.php:698`
  ```php
  function grade_get_setting($courseid, $name, $default=null, $resetcache=false) {
  ```
- `public/lib/gradelib.php:736`
  ```php
  function grade_get_settings($courseid) {
  ```
- `public/lib/gradelib.php:758`
  ```php
  function grade_set_setting($courseid, $name, $value) {
  ```
- `public/lib/gradelib.php:791`
  ```php
  function grade_format_gradevalue(?float $value, &$grade_item, $localized=true, $displaytype=null, $decimals=null) {
  ```
- `public/lib/gradelib.php:861`
  ```php
  function grade_format_gradevalue_real(?float $value, $grade_item, $decimals, $localized) {
  ```
- `public/lib/gradelib.php:884`
  ```php
  function grade_format_gradevalue_percentage(?float $value, $grade_item, $decimals, $localized) {
  ```
- `public/lib/gradelib.php:903`
  ```php
  function grade_format_gradevalue_letter(?float $value, $grade_item) {
  ```
- `public/lib/gradelib.php:941`
  ```php
  function grade_get_categories_menu($courseid, $includenew=false) {
  ```
- `public/lib/gradelib.php:975`
  ```php
  function grade_get_letters($context=null) {
  ```
- `public/lib/tests/db/upgradelib_test.php:146`
  ```php
  public function test_upgrade_block_set_defaultregion_create_missing(): void {
  ```
- `public/lib/tests/db/upgradelib_test.php:220`
  ```php
  public function test_upgrade_block_delete_instances(): void {
  ```
- `public/lib/tests/db/upgradelib_test.php:34`
  ```php
  public function setUp(): void {
  ```
- `public/lib/tests/db/upgradelib_test.php:453`
  ```php
  public function test_upgrade_block_set_my_user_parent_context(): void {
  ```
- `public/lib/tests/db/upgradelib_test.php:49`
  ```php
  public function test_upgrade_block_set_defaultregion(): void {
  ```
- `public/lib/tests/db/upgradelib_test.php:549`
  ```php
  public function test_upgrade_create_async_mimetype_upgrade_task(): void {
  ```
- `public/lib/tests/event/grade_deleted_test.php:34`
  ```php
  public function test_event(): void {
  ```
- `public/lib/tests/event/grade_item_deleted_test.php:44`
  ```php
  public function test_grade_item_deleted(): void {
  ```
- `public/lib/tests/event/user_graded_test.php:38`
  ```php
  public function setUp(): void {
  ```
- `public/lib/tests/event/user_graded_test.php:46`
  ```php
  public function test_event(): void {
  ```
- `public/lib/tests/event/user_graded_test.php:91`
  ```php
  public function test_event_is_triggered(): void {
  ```
- `public/lib/tests/gradelib_test.php:129`
  ```php
  public function test_remove_grade_letters(): void {
  ```
- `public/lib/tests/gradelib_test.php:172`
  ```php
  public function test_grade_course_category_delete(): void {
  ```
- `public/lib/tests/gradelib_test.php:195`
  ```php
  public function test_grade_regrade_final_grades(): void {
  ```
- `public/lib/tests/gradelib_test.php:247`
  ```php
  public function test_grade_get_date_for_user_grade(\stdClass $grade, \stdClass $user, ?int $expected): void {
  ```
- `public/lib/tests/gradelib_test.php:256`
  ```php
  public static function grade_get_date_for_user_grade_provider(): array {
  ```
- `public/lib/tests/gradelib_test.php:319`
  ```php
  public function test_get_grade_letters(): void {
  ```
- `public/lib/tests/gradelib_test.php:344`
  ```php
  public function test_get_grade_letters_custom(): void {
  ```
- `public/lib/tests/gradelib_test.php:34`
  ```php
  public function test_grade_update_mod_grades(): void {
  ```
- `public/lib/tests/gradelib_test.php:377`
  ```php
  public function test_grade_get_grades_errors(): void {
  ```
- `public/lib/tests/gradelib_test.php:71`
  ```php
  public function test_is_gradable(array $gradetypes, bool $expected): void {
  ```
- `public/lib/tests/gradelib_test.php:97`
  ```php
  public static function graditems_provider(): array {
  ```
- `public/lib/tests/grades_external_test.php:130`
  ```php
  public function test_update_grades(): void {
  ```
- `public/lib/tests/grades_external_test.php:41`
  ```php
  protected function load_test_data($assignmentname, $student1rawgrade, $student2rawgrade) {
  ```
- `public/lib/tests/upgrade_util_test.php:108`
  ```php
  public function test_can_use_tls12($sslversion, $uname, $expected): void {
  ```
- `public/lib/tests/upgrade_util_test.php:128`
  ```php
  public static function can_use_tls12_testcases(): array {
  ```
- `public/lib/tests/upgrade_util_test.php:46`
  ```php
  public function test_validate_php_curl_tls($curlinfo, $zts, $expected): void {
  ```
- `public/lib/tests/upgrade_util_test.php:53`
  ```php
  public static function validate_php_curl_tls_testcases(): array {
  ```
- `public/lib/tests/upgradelib_test.php:1004`
  ```php
  public function test_upgrade_calendar_standard_events_fix(): void {
  ```
- `public/lib/tests/upgradelib_test.php:1049`
  ```php
  public function test_upgrade_calendar_subscription_events_fix(): void {
  ```
- `public/lib/tests/upgradelib_test.php:1116`
  ```php
  public function test_upgrade_calendar_action_events_fix(): void {
  ```
- `public/lib/tests/upgradelib_test.php:1172`
  ```php
  public function test_upgrade_calendar_user_override_events_fix(): void {
  ```
- `public/lib/tests/upgradelib_test.php:1234`
  ```php
  public function test_upgrade_calendar_group_override_events_fix(): void {
  ```
- `public/lib/tests/upgradelib_test.php:1289`
  ```php
  public function test_admin_dir_usage_not_set(): void {
  ```
- `public/lib/tests/upgradelib_test.php:1298`
  ```php
  public function test_admin_dir_usage_is_default(): void {
  ```
- `public/lib/tests/upgradelib_test.php:1310`
  ```php
  public function test_admin_dir_usage_non_standard(): void {
  ```
- `public/lib/tests/upgradelib_test.php:1327`
  ```php
  public function test_check_xmlrpc_webservice_is_not_set(): void {
  ```
- `public/lib/tests/upgradelib_test.php:1345`
  ```php
  public function test_check_xmlrpc_webservice_is_set(): void {
  ```
- `public/lib/tests/upgradelib_test.php:1363`
  ```php
  public function test_check_mod_assignment_is_used(): void {
  ```
- `public/lib/tests/upgradelib_test.php:1398`
  ```php
  public static function usermenu_items_dataprovider(): array {
  ```
- `public/lib/tests/upgradelib_test.php:1431`
  ```php
  public function test_upgrade_add_item_to_usermenu(string $initialmenu, string $newmenuitem, string $expectedmenu): void {
  ```
- `public/lib/tests/upgradelib_test.php:1448`
  ```php
  public function test_upgrade_fix_file_timestamps(): void {
  ```
- `public/lib/tests/upgradelib_test.php:148`
  ```php
  public function test_upgrade_calculated_grade_items_freeze(): void {
  ```
- `public/lib/tests/upgradelib_test.php:1513`
  ```php
  public function test_moodle_upgrade_check_outageless(): void {
  ```
- `public/lib/tests/upgradelib_test.php:1536`
  ```php
  public function test_moodle_start_upgrade_outageless(): void {
  ```
- `public/lib/tests/upgradelib_test.php:1560`
  ```php
  public function test_moodle_set_upgrade_timeout_outageless(): void {
  ```
- `public/lib/tests/upgradelib_test.php:1586`
  ```php
  public function test_upgrade_components_with_outageless(): void {
  ```
- `public/lib/tests/upgradelib_test.php:1617`
  ```php
  public static function upgrade_change_binary_column_to_int_provider(): array {
  ```
- `public/lib/tests/upgradelib_test.php:1651`
  ```php
  public function test_upgrade_change_binary_column_to_int(
  ```
- `public/lib/tests/upgradelib_test.php:1712`
  ```php
  public function test_upgrade_store_relative_url_sitehomepage(): void {
  ```
- `public/lib/tests/upgradelib_test.php:1740`
  ```php
  public function test_check_aurora_version_is_not_used(): void {
  ```
- `public/lib/tests/upgradelib_test.php:1755`
  ```php
  public function test_check_aurora_version_is_used(): void {
  ```
- `public/lib/tests/upgradelib_test.php:1772`
  ```php
  public function test_moodlenet_migrate_profile_field(): void {
  ```
- `public/lib/tests/upgradelib_test.php:282`
  ```php
  public function test_upgrade_update_category_grademax_regrade_final_grades(): void {
  ```
- `public/lib/tests/upgradelib_test.php:328`
  ```php
  function test_upgrade_calculated_grade_items_regrade(): void {
  ```
- `public/lib/tests/upgradelib_test.php:399`
  ```php
  public function test_upgrade_course_letter_boundary(): void {
  ```
- `public/lib/tests/upgradelib_test.php:41`
  ```php
  public function test_upgrade_stale_php_files_present(): void {
  ```
- `public/lib/tests/upgradelib_test.php:603`
  ```php
  public function test_upgrade_letter_boundary_needs_freeze(): void {
  ```
- `public/lib/tests/upgradelib_test.php:60`
  ```php
  private function insert_fake_grade_item_sortorder($courseid, $sortorder) {
  ```
- `public/lib/tests/upgradelib_test.php:636`
  ```php
  private function assign_bad_letter_boundary($contextid) {
  ```
- `public/lib/tests/upgradelib_test.php:664`
  ```php
  private function assign_good_letter_boundary($contextid) {
  ```
- `public/lib/tests/upgradelib_test.php:690`
  ```php
  public function test_check_libcurl_version(): void {
  ```
- `public/lib/tests/upgradelib_test.php:708`
  ```php
  public function create_testthemes() {
  ```
- `public/lib/tests/upgradelib_test.php:747`
  ```php
  public static function serialized_strings_dataprovider(): array {
  ```
- `public/lib/tests/upgradelib_test.php:785`
  ```php
  public function test_upgrade_fix_serialized_objects($initialstring, $expectededited, $expectedresult): void {
  ```
- `public/lib/tests/upgradelib_test.php:794`
  ```php
  public function encoded_strings_dataprovider() {
  ```
- `public/lib/tests/upgradelib_test.php:80`
  ```php
  public function test_upgrade_extra_credit_weightoverride(): void {
  ```
- `public/lib/tests/upgradelib_test.php:814`
  ```php
  public function test_upgrade_delete_orphaned_file_records(): void {
  ```
- `public/lib/tests/upgradelib_test.php:900`
  ```php
  public function test_upgrade_core_licenses(): void {
  ```
- `public/lib/tests/upgradelib_test.php:931`
  ```php
  public function run_upgrade_step_query() {
  ```
- `public/lib/tests/upgradelib_test.php:940`
  ```php
  public function test_upgrade_calendar_events_status(): void {
  ```
- `public/lib/tests/upgradelib_test.php:975`
  ```php
  public function test_upgrade_calendar_events_get_teacherid(): void {
  ```
- `public/lib/upgradelib.php:102`
  ```php
  public function __construct($plugin, $pluginversion) {
  ```
- `public/lib/upgradelib.php:1154`
  ```php
  function log_update_descriptions($component) {
  ```
- `public/lib/upgradelib.php:120`
  ```php
  function __construct($plugin, $details) {
  ```
- `public/lib/upgradelib.php:1211`
  ```php
  function external_update_descriptions($component) {
  ```
- `public/lib/upgradelib.php:1418`
  ```php
  function external_update_services() {
  ```
- `public/lib/upgradelib.php:143`
  ```php
  public function __construct($component, $expected, $current) {
  ```
- `public/lib/upgradelib.php:1463`
  ```php
  function upgrade_handle_exception($ex, $plugin = null) {
  ```
- `public/lib/upgradelib.php:1490`
  ```php
  function upgrade_log($type, $plugin, $info, $details=null, $backtrace=null) {
  ```
- `public/lib/upgradelib.php:1554`
  ```php
  function upgrade_started($preinstall=false) {
  ```
- `public/lib/upgradelib.php:1592`
  ```php
  function upgrade_finished_handler() {
  ```
- `public/lib/upgradelib.php:1604`
  ```php
  function upgrade_finished($continueurl=null) {
  ```
- `public/lib/upgradelib.php:1629`
  ```php
  function upgrade_setup_debug($starting) {
  ```
- `public/lib/upgradelib.php:1646`
  ```php
  function print_upgrade_separator() {
  ```
- `public/lib/upgradelib.php:1657`
  ```php
  function print_upgrade_part_start($plugin, $installation, $verbose) {
  ```
- `public/lib/upgradelib.php:1691`
  ```php
  function print_upgrade_part_end($plugin, $installation, $verbose) {
  ```
- `public/lib/upgradelib.php:1717`
  ```php
  function upgrade_init_javascript() {
  ```
- `public/lib/upgradelib.php:1730`
  ```php
  function upgrade_language_pack($lang = null) {
  ```
- `public/lib/upgradelib.php:1770`
  ```php
  function upgrade_themes() {
  ```
- `public/lib/upgradelib.php:1797`
  ```php
  function install_core($version, $verbose) {
  ```
- `public/lib/upgradelib.php:1860`
  ```php
  function upgrade_core($version, $verbose) {
  ```
- `public/lib/upgradelib.php:187`
  ```php
  public static function record_start(bool $installation = false): void {
  ```
- `public/lib/upgradelib.php:1939`
  ```php
  function upgrade_noncore($verbose) {
  ```
- `public/lib/upgradelib.php:199`
  ```php
  public static function record_end(bool $verbose = true): void {
  ```
- `public/lib/upgradelib.php:2007`
  ```php
  function core_tables_exist() {
  ```
- `public/lib/upgradelib.php:2030`
  ```php
  function upgrade_plugin_mnet_functions($component) {
  ```
- `public/lib/upgradelib.php:218`
  ```php
  public static function record_savepoint($version) {
  ```
- `public/lib/upgradelib.php:2211`
  ```php
  function admin_mnet_method_profile(ReflectionFunctionAbstract $function) {
  ```
- `public/lib/upgradelib.php:2243`
  ```php
  function admin_mnet_method_get_docblock(ReflectionFunctionAbstract $function) {
  ```
- `public/lib/upgradelib.php:2265`
  ```php
  function admin_mnet_method_get_help(ReflectionFunctionAbstract $function) {
  ```
- `public/lib/upgradelib.php:2281`
  ```php
  function check_database_storage_engine(environment_results $result) {
  ```
- `public/lib/upgradelib.php:2305`
  ```php
  function check_slasharguments(environment_results $result){
  ```
- `public/lib/upgradelib.php:2323`
  ```php
  function check_database_tables_row_format(environment_results $result) {
  ```
- `public/lib/upgradelib.php:2355`
  ```php
  function check_mysql_file_format(environment_results $result) {
  ```
- `public/lib/upgradelib.php:236`
  ```php
  public static function record_detail(string $detail, bool $showalways = false): void {
  ```
- `public/lib/upgradelib.php:2380`
  ```php
  function check_mysql_file_per_table(environment_results $result) {
  ```
- `public/lib/upgradelib.php:2405`
  ```php
  function check_mysql_large_prefix(environment_results $result) {
  ```
- `public/lib/upgradelib.php:2430`
  ```php
  function check_mysql_incomplete_unicode_support(environment_results $result) {
  ```
- `public/lib/upgradelib.php:2456`
  ```php
  function check_is_https(environment_results $result) {
  ```
- `public/lib/upgradelib.php:2474`
  ```php
  function check_sixtyfour_bits(environment_results $result) {
  ```
- `public/lib/upgradelib.php:2490`
  ```php
  function check_db_prefix_length(environment_results $result) {
  ```
- `public/lib/upgradelib.php:2516`
  ```php
  function check_upgrade_key($upgradekeyhash) {
  ```
- `public/lib/upgradelib.php:2552`
  ```php
  function upgrade_install_plugins(array $installable, $confirmed, $heading='', $continue=null, $return=null) {
  ```
- `public/lib/upgradelib.php:2610`
  ```php
  function check_unoconv_version(environment_results $result) {
  ```
- `public/lib/upgradelib.php:2644`
  ```php
  function check_tls_libraries(environment_results $result) {
  ```
- `public/lib/upgradelib.php:264`
  ```php
  public static function get_elapsed() {
  ```
- `public/lib/upgradelib.php:2674`
  ```php
  function check_libcurl_version(environment_results $result) {
  ```
- `public/lib/upgradelib.php:2709`
  ```php
  function check_max_input_vars(environment_results $result) {
  ```
- `public/lib/upgradelib.php:2729`
  ```php
  function check_admin_dir_usage(environment_results $result): ?environment_results {
  ```
- `public/lib/upgradelib.php:2756`
  ```php
  function check_xmlrpc_usage(environment_results $result): ?environment_results {
  ```
- `public/lib/upgradelib.php:2778`
  ```php
  function check_mod_assignment(environment_results $result): ?environment_results {
  ```
- `public/lib/upgradelib.php:278`
  ```php
  function upgrade_set_timeout($max_execution_time=300) {
  ```
- `public/lib/upgradelib.php:2806`
  ```php
  function check_async_backup(environment_results $result): ?environment_results {
  ```
- `public/lib/upgradelib.php:2826`
  ```php
  function check_aurora_version(environment_results $result): ?environment_results {
  ```
- `public/lib/upgradelib.php:337`
  ```php
  function upgrade_main_savepoint($result, $version, $allowabort=true) {
  ```
- `public/lib/upgradelib.php:380`
  ```php
  function upgrade_mod_savepoint($result, $version, $modname, $allowabort=true) {
  ```
- `public/lib/upgradelib.php:395`
  ```php
  function upgrade_block_savepoint($result, $version, $blockname, $allowabort=true) {
  ```
- `public/lib/upgradelib.php:411`
  ```php
  function upgrade_plugin_savepoint($result, $version, $type, $plugin, $allowabort=true) {
  ```
- `public/lib/upgradelib.php:454`
  ```php
  function upgrade_stale_php_files_present(): bool {
  ```
- `public/lib/upgradelib.php:45`
  ```php
  function __construct($plugin, $version, $debuginfo=NULL) {
  ```
- `public/lib/upgradelib.php:61`
  ```php
  function __construct($plugin, $oldversion, $newversion) {
  ```
- `public/lib/upgradelib.php:627`
  ```php
  function upgrade_component_updated(string $component, string $messageplug = '',
  ```
- `public/lib/upgradelib.php:664`
  ```php
  function upgrade_plugins($type, $startcallback, $endcallback, $verbose) {
  ```
- `public/lib/upgradelib.php:76`
  ```php
  function __construct($plugin, $pluginversion, $currentmoodle, $requiremoodle) {
  ```
- `public/lib/upgradelib.php:804`
  ```php
  function upgrade_plugins_modules($startcallback, $endcallback, $verbose) {
  ```
- `public/lib/upgradelib.php:963`
  ```php
  function upgrade_plugins_blocks($startcallback, $endcallback, $verbose) {
  ```
- `public/local/kopere_bi/db/upgrade.php:34`
  ```php
  function xmldb_local_kopere_bi_upgrade($oldversion) {
  ```
- `public/local/kopere_dashboard/db/upgrade.php:40`
  ```php
  function xmldb_local_kopere_dashboard_upgrade($oldversion) {
  ```
- `public/media/player/videojs/db/upgrade.php:51`
  ```php
  function xmldb_media_videojs_upgrade($oldversion) {
  ```
- `public/message/output/email/db/upgrade.php:30`
  ```php
  function xmldb_message_email_upgrade($oldversion) {
  ```
- `public/message/output/popup/db/upgrade.php:30`
  ```php
  function xmldb_message_popup_upgrade($oldversion) {
  ```
- `public/mod/assign/classes/event/submission_graded.php:104`
  ```php
  protected function validate_data() {
  ```
- `public/mod/assign/classes/event/submission_graded.php:116`
  ```php
  public static function get_objectid_mapping() {
  ```
- `public/mod/assign/classes/event/submission_graded.php:53`
  ```php
  public static function create_from_grade(\assign $assign, \stdClass $grade) {
  ```
- `public/mod/assign/classes/event/submission_graded.php:73`
  ```php
  public function get_description() {
  ```
- `public/mod/assign/classes/event/submission_graded.php:83`
  ```php
  public static function get_name() {
  ```
- `public/mod/assign/classes/event/submission_graded.php:92`
  ```php
  protected function init() {
  ```
- `public/mod/assign/classes/grades/gradeitems.php:46`
  ```php
  public static function get_itemname_mapping_for_component(): array {
  ```
- `public/mod/assign/classes/grades/gradeitems.php:57`
  ```php
  public static function get_advancedgrading_itemnames(): array {
  ```
- `public/mod/assign/db/upgrade.php:30`
  ```php
  function xmldb_assign_upgrade($oldversion) {
  ```
- `public/mod/assign/feedback/comments/db/upgrade.php:30`
  ```php
  function xmldb_assignfeedback_comments_upgrade($oldversion) {
  ```
- `public/mod/assign/feedback/editpdf/db/upgrade.php:30`
  ```php
  function xmldb_assignfeedback_editpdf_upgrade($oldversion) {
  ```
- `public/mod/assign/feedback/file/db/upgrade.php:30`
  ```php
  function xmldb_assignfeedback_file_upgrade($oldversion) {
  ```
- `public/mod/assign/feedback/offline/importgradesform.php:42`
  ```php
  public function definition() {
  ```
- `public/mod/assign/feedback/offline/importgradeslib.php:156`
  ```php
  public function get_encoding() {
  ```
- `public/mod/assign/feedback/offline/importgradeslib.php:165`
  ```php
  public function get_separator() {
  ```
- `public/mod/assign/feedback/offline/importgradeslib.php:174`
  ```php
  public function next() {
  ```
- `public/mod/assign/feedback/offline/importgradeslib.php:209`
  ```php
  public function close($delete) {
  ```
- `public/mod/assign/feedback/offline/importgradeslib.php:72`
  ```php
  public function __construct($importid, assign $assignment, $encoding = 'utf-8', $separator = 'comma') {
  ```
- `public/mod/assign/feedback/offline/importgradeslib.php:86`
  ```php
  public function parsecsv($csvdata) {
  ```
- `public/mod/assign/feedback/offline/importgradeslib.php:96`
  ```php
  public function init() {
  ```
- `public/mod/assign/feedback/offline/uploadgradesform.php:40`
  ```php
  public function definition() {
  ```
- `public/mod/assign/gradeform.php:46`
  ```php
  public function definition() {
  ```
- `public/mod/assign/gradeform.php:66`
  ```php
  protected function get_form_identifier() {
  ```
- `public/mod/assign/gradeform.php:76`
  ```php
  public function validation($data, $files) {
  ```
- `public/mod/assign/submission/comments/db/upgrade.php:30`
  ```php
  function xmldb_assignsubmission_comments_upgrade($oldversion) {
  ```
- `public/mod/assign/submission/file/db/upgrade.php:30`
  ```php
  function xmldb_assignsubmission_file_upgrade($oldversion) {
  ```
- `public/mod/assign/submission/onlinetext/db/upgrade.php:30`
  ```php
  function xmldb_assignsubmission_onlinetext_upgrade($oldversion) {
  ```
- `public/mod/attendance/db/upgrade.php:33`
  ```php
  function xmldb_attendance_upgrade($oldversion = 0) {
  ```
- `public/mod/attendance/db/upgradelib.php:28`
  ```php
  function attendance_upgrade_create_calendar_events() {
  ```
- `public/mod/bigbluebuttonbn/classes/task/upgrade_recordings_task.php:166`
  ```php
  protected function get_sql_query_for_logs(string $meetingid, bool $isimported): array {
  ```
- `public/mod/bigbluebuttonbn/classes/task/upgrade_recordings_task.php:191`
  ```php
  public static function schedule_upgrade_per_meeting($importedrecordings = false) {
  ```
- `public/mod/bigbluebuttonbn/classes/task/upgrade_recordings_task.php:39`
  ```php
  public function execute() {
  ```
- `public/mod/bigbluebuttonbn/classes/task/upgrade_recordings_task.php:55`
  ```php
  protected function process_bigbluebuttonbn_logs(string $meetingid, bool $isimported): bool {
  ```
- `public/mod/bigbluebuttonbn/db/upgrade.php:126`
  ```php
  function xmldb_bigbluebuttonbn_add_change_field(
  ```
- `public/mod/bigbluebuttonbn/db/upgrade.php:168`
  ```php
  function xmldb_bigbluebuttonbn_index_table(
  ```
- `public/mod/bigbluebuttonbn/db/upgrade.php:37`
  ```php
  function xmldb_bigbluebuttonbn_upgrade($oldversion = 0) {
  ```
- `public/mod/bigbluebuttonbn/tests/task/upgrade_recordings_task_test.php:133`
  ```php
  public function test_upgrade_recordings_imported_basic(): void {
  ```
- `public/mod/bigbluebuttonbn/tests/task/upgrade_recordings_task_test.php:197`
  ```php
  public function test_upgrade_recordings_with_missing_recording_on_bbb_server(): void {
  ```
- `public/mod/bigbluebuttonbn/tests/task/upgrade_recordings_task_test.php:245`
  ```php
  public function test_upgrade_recordings_with_more_recordings_on_bbb_server(): void {
  ```
- `public/mod/bigbluebuttonbn/tests/task/upgrade_recordings_task_test.php:293`
  ```php
  protected function setup_basic_data($importedrecording = false) {
  ```
- `public/mod/bigbluebuttonbn/tests/task/upgrade_recordings_task_test.php:317`
  ```php
  protected function setup_basic_course_and_meeting() {
  ```
- `public/mod/bigbluebuttonbn/tests/task/upgrade_recordings_task_test.php:344`
  ```php
  protected function create_meeting_for_logs(?array $groups = null) {
  ```
- `public/mod/bigbluebuttonbn/tests/task/upgrade_recordings_task_test.php:51`
  ```php
  public function setUp(): void {
  ```
- `public/mod/bigbluebuttonbn/tests/task/upgrade_recordings_task_test.php:60`
  ```php
  public function test_upgrade_recordings_basic(): void {
  ```
- `public/mod/book/db/upgrade.php:30`
  ```php
  function xmldb_book_upgrade($oldversion) {
  ```
- `public/mod/certificate/db/upgrade.php:26`
  ```php
  function xmldb_certificate_upgrade($oldversion=0) {
  ```
- `public/mod/choice/db/upgrade.php:42`
  ```php
  function xmldb_choice_upgrade($oldversion) {
  ```
- `public/mod/data/db/upgrade.php:42`
  ```php
  function xmldb_data_upgrade($oldversion) {
  ```
- `public/mod/feedback/db/upgrade.php:42`
  ```php
  function xmldb_feedback_upgrade($oldversion) {
  ```
- `public/mod/folder/db/upgrade.php:45`
  ```php
  function xmldb_folder_upgrade($oldversion) {
  ```
- `public/mod/forum/classes/grades/forum_gradeitem.php:103`
  ```php
  public function user_can_grade(stdClass $gradeduser, stdClass $grader): bool {
  ```
- `public/mod/forum/classes/grades/forum_gradeitem.php:118`
  ```php
  public function require_user_can_grade(stdClass $gradeduser, stdClass $grader): void {
  ```
- `public/mod/forum/classes/grades/forum_gradeitem.php:130`
  ```php
  protected function get_gradeitem_value(): int {
  ```
- `public/mod/forum/classes/grades/forum_gradeitem.php:144`
  ```php
  public function create_empty_grade(stdClass $gradeduser, stdClass $grader): stdClass {
  ```
- `public/mod/forum/classes/grades/forum_gradeitem.php:168`
  ```php
  public function get_grade_for_user(stdClass $gradeduser, ?stdClass $grader = null): ?stdClass {
  ```
- `public/mod/forum/classes/grades/forum_gradeitem.php:195`
  ```php
  public function user_has_grade(stdClass $gradeduser): bool {
  ```
- `public/mod/forum/classes/grades/forum_gradeitem.php:218`
  ```php
  public function get_all_grades(): array {
  ```
- `public/mod/forum/classes/grades/forum_gradeitem.php:235`
  ```php
  public function get_grade_instance_id(): int {
  ```
- `public/mod/forum/classes/grades/forum_gradeitem.php:244`
  ```php
  public function should_grade_only_active_users(): bool {
  ```
- `public/mod/forum/classes/grades/forum_gradeitem.php:265`
  ```php
  protected function store_grade(stdClass $grade): bool {
  ```
- `public/mod/forum/classes/grades/forum_gradeitem.php:54`
  ```php
  public static function load_from_context(context $context): parent {
  ```
- `public/mod/forum/classes/grades/forum_gradeitem.php:71`
  ```php
  public static function load_from_forum_entity(forum_entity $forum): self {
  ```
- `public/mod/forum/classes/grades/forum_gradeitem.php:83`
  ```php
  protected function get_table_name(): string {
  ```
- `public/mod/forum/classes/grades/forum_gradeitem.php:92`
  ```php
  public function is_grading_enabled(): bool {
  ```
- `public/mod/forum/classes/grades/gradeitems.php:46`
  ```php
  public static function get_itemname_mapping_for_component(): array {
  ```
- `public/mod/forum/classes/grades/gradeitems.php:58`
  ```php
  public static function get_advancedgrading_itemnames(): array {
  ```
- `public/mod/forum/db/upgrade.php:43`
  ```php
  function xmldb_forum_upgrade($oldversion) {
  ```
- `public/mod/forum/tests/grade/forum_gradeitem_test.php:120`
  ```php
  public function test_get_and_store_grade_for_user_with_rubric(): void {
  ```
- `public/mod/forum/tests/grade/forum_gradeitem_test.php:184`
  ```php
  public function test_should_grade_only_active_users(bool $showonlyactiveenrolconfig, bool $showonlyactiveenrolpreference,
  ```
- `public/mod/forum/tests/grade/forum_gradeitem_test.php:219`
  ```php
  public static function should_grade_only_active_users_provider(): array {
  ```
- `public/mod/forum/tests/grade/forum_gradeitem_test.php:265`
  ```php
  protected function get_forum_instance(array $config = []): forum_entity {
  ```
- `public/mod/forum/tests/grade/forum_gradeitem_test.php:50`
  ```php
  public function test_get_grade_for_user_exists(): void {
  ```
- `public/mod/forum/tests/grade/forum_gradeitem_test.php:70`
  ```php
  public function test_user_has_grade(): void {
  ```
- `public/mod/forum/tests/grade/forum_gradeitem_test.php:97`
  ```php
  public function test_get_and_store_grade_for_user_with_simple_direct_grade(): void {
  ```
- `public/mod/forum/tests/grade/gradeitems_test.php:38`
  ```php
  public function test_get_itemname_mapping_for_component(): void {
  ```
- `public/mod/forum/tests/grade/gradeitems_test.php:53`
  ```php
  public function test_get_advancedgrading_itemnames_for_component(): void {
  ```
- `public/mod/forum/tests/grade/gradeitems_test.php:68`
  ```php
  public function test_is_advancedgrading_itemname(string $itemname, bool $expected): void {
  ```
- `public/mod/forum/tests/grade/gradeitems_test.php:80`
  ```php
  public static function is_advancedgrading_itemname_provider(): array {
  ```
- `public/mod/glossary/db/upgrade.php:42`
  ```php
  function xmldb_glossary_upgrade($oldversion) {
  ```
- `public/mod/h5pactivity/classes/local/grader.php:115`
  ```php
  public function update_grades(int $userid = 0): void {
  ```
- `public/mod/h5pactivity/classes/local/grader.php:144`
  ```php
  private function get_user_grades_for_gradebook(int $userid = 0): array {
  ```
- `public/mod/h5pactivity/classes/local/grader.php:190`
  ```php
  private function get_user_grades_for_deletion(int $userid = 0): array {
  ```
- `public/mod/h5pactivity/classes/local/grader.php:55`
  ```php
  public function __construct(stdClass $instance, string $idnumber = '') {
  ```
- `public/mod/h5pactivity/classes/local/grader.php:65`
  ```php
  public function grade_item_delete(): ?int {
  ```
- `public/mod/h5pactivity/classes/local/grader.php:79`
  ```php
  public function grade_item_update($grades = null): int {
  ```
- `public/mod/h5pactivity/db/upgrade.php:47`
  ```php
  function xmldb_h5pactivity_upgrade($oldversion) {
  ```
- `public/mod/h5pactivity/tests/local/grader_test.php:164`
  ```php
  public static function grade_item_update_data(): array {
  ```
- `public/mod/h5pactivity/tests/local/grader_test.php:202`
  ```php
  public function test_update_grades(int $newgrade, bool $all, int $completion, array $results): void {
  ```
- `public/mod/h5pactivity/tests/local/grader_test.php:263`
  ```php
  public static function update_grades_data(): array {
  ```
- `public/mod/h5pactivity/tests/local/grader_test.php:342`
  ```php
  private function generate_fake_attempt(stdClass $activity, stdClass $user,
  ```
- `public/mod/h5pactivity/tests/local/grader_test.php:44`
  ```php
  public static function setupBeforeClass(): void {
  ```
- `public/mod/h5pactivity/tests/local/grader_test.php:52`
  ```php
  public function test_grade_item_delete(): void {
  ```
- `public/mod/h5pactivity/tests/local/grader_test.php:85`
  ```php
  public function test_grade_item_update(int $newgrade, bool $reset, string $idnumber): void {
  ```
- `public/mod/imscp/db/upgrade.php:29`
  ```php
  function xmldb_imscp_upgrade($oldversion) {
  ```
- `public/mod/label/db/upgrade.php:45`
  ```php
  function xmldb_label_upgrade($oldversion) {
  ```
- `public/mod/lesson/db/upgrade.php:50`
  ```php
  function xmldb_lesson_upgrade($oldversion) {
  ```
- `public/mod/lti/classes/output/registration_upgrade_choice_page.php:55`
  ```php
  public function __construct(array $tools, string $startregurl) {
  ```
- `public/mod/lti/classes/output/registration_upgrade_choice_page.php:65`
  ```php
  public function export_for_template(renderer_base $output) {
  ```
- `public/mod/lti/db/upgrade.php:59`
  ```php
  function xmldb_lti_upgrade($oldversion) {
  ```
- `public/mod/lti/service/gradebookservices/backup/moodle2/backup_ltiservice_gradebookservices_subplugin.class.php:49`
  ```php
  protected function define_lti_subplugin_structure() {
  ```
- `public/mod/lti/service/gradebookservices/backup/moodle2/restore_ltiservice_gradebookservices_subplugin.class.php:135`
  ```php
  private function find_proxy_id($data) {
  ```
- `public/mod/lti/service/gradebookservices/backup/moodle2/restore_ltiservice_gradebookservices_subplugin.class.php:158`
  ```php
  private function find_typeid($data, $courseid) {
  ```
- `public/mod/lti/service/gradebookservices/backup/moodle2/restore_ltiservice_gradebookservices_subplugin.class.php:205`
  ```php
  protected function after_restore_lti() {
  ```
- `public/mod/lti/service/gradebookservices/backup/moodle2/restore_ltiservice_gradebookservices_subplugin.class.php:50`
  ```php
  protected function define_lti_subplugin_structure() {
  ```
- `public/mod/lti/service/gradebookservices/backup/moodle2/restore_ltiservice_gradebookservices_subplugin.class.php:65`
  ```php
  public function process_ltiservice_gradebookservices_lineitem($data) {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/resources/lineitem.php:126`
  ```php
  private function get_request($response, $item, $typeid) {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/resources/lineitem.php:145`
  ```php
  private function process_put_request($body, $olditem, $typeid) {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/resources/lineitem.php:296`
  ```php
  private function process_delete_request($item) {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/resources/lineitem.php:321`
  ```php
  public function parse_value($value) {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/resources/lineitem.php:47`
  ```php
  public function __construct($service) {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/resources/lineitem.php:65`
  ```php
  public function execute($response) {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/resources/lineitems.php:145`
  ```php
  private function get_json_for_get_request($items, $resourceid, $ltilinkid,
  ```
- `public/mod/lti/service/gradebookservices/classes/local/resources/lineitems.php:230`
  ```php
  private function get_json_for_post_request($body, $contextid, $typeid) {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/resources/lineitems.php:287`
  ```php
  public function parse_value($value) {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/resources/lineitems.php:47`
  ```php
  public function __construct($service) {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/resources/lineitems.php:65`
  ```php
  public function execute($response) {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/resources/results.php:153`
  ```php
  private function get_json_for_get_request($itemid, $limitfrom, $limitnum, $useridfilter, $typeid, $response) {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/resources/results.php:251`
  ```php
  public function parse_value($value) {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/resources/results.php:47`
  ```php
  public function __construct($service) {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/resources/results.php:62`
  ```php
  public function execute($response) {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/resources/scores.php:172`
  ```php
  private function get_json_for_post_request($response, $body, $item, $contextid, $typeid) {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/resources/scores.php:216`
  ```php
  public function parse_value($value) {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/resources/scores.php:47`
  ```php
  public function __construct($service) {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/resources/scores.php:64`
  ```php
  public function execute($response) {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/service/gradebookservices.php:100`
  ```php
  public function get_permitted_scopes() {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/service/gradebookservices.php:124`
  ```php
  public function get_scopes() {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/service/gradebookservices.php:134`
  ```php
  public function get_configuration_options(&$mform) {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/service/gradebookservices.php:161`
  ```php
  public function override_endpoint(string $messagetype, string $targetlinkuri, ?string $customstr, int $courseid,
  ```
- `public/mod/lti/service/gradebookservices/classes/local/service/gradebookservices.php:192`
  ```php
  public function get_jwt_claim_mappings(): array {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/service/gradebookservices.php:253`
  ```php
  public function get_launch_parameters($messagetype, $courseid, $user, $typeid, $modlti = null) {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/service/gradebookservices.php:310`
  ```php
  public function get_lineitems($courseid, $resourceid, $ltilinkid, $tag, $limitfrom, $limitnum, $typeid) {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/service/gradebookservices.php:390`
  ```php
  public function get_lineitem($courseid, $itemid, $typeid) {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/service/gradebookservices.php:447`
  ```php
  public function add_standalone_lineitem(string $courseid, string $label, float $maximumscore,
  ```
- `public/mod/lti/service/gradebookservices/classes/local/service/gradebookservices.php:485`
  ```php
  public static function save_score($gradeitem, $score, $userid) {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/service/gradebookservices.php:499`
  ```php
  public function save_grade_item($gradeitem, $score, $userid) {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/service/gradebookservices.php:567`
  ```php
  public static function item_for_json($item, $endpoint, $typeid) {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/service/gradebookservices.php:617`
  ```php
  public static function result_for_json($grade, $endpoint, $typeid) {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/service/gradebookservices.php:652`
  ```php
  public static function check_lti_id($linkid, $course, $toolproxy) {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/service/gradebookservices.php:67`
  ```php
  public function __construct() {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/service/gradebookservices.php:689`
  ```php
  public static function check_lti_1x_id($linkid, $course, $typeid) {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/service/gradebookservices.php:731`
  ```php
  public static function update_coupled_gradebookservices(object $ltiinstance,
  ```
- `public/mod/lti/service/gradebookservices/classes/local/service/gradebookservices.php:771`
  ```php
  public function instance_added(object $lti): void {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/service/gradebookservices.php:782`
  ```php
  public function instance_updated(object $lti): void {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/service/gradebookservices.php:793`
  ```php
  public function set_instance_form_values(object $defaultvalues): void {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/service/gradebookservices.php:80`
  ```php
  public function get_resources() {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/service/gradebookservices.php:817`
  ```php
  public static function delete_orphans_ltiservice_gradebookservices_rows() {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/service/gradebookservices.php:834`
  ```php
  public static function is_user_gradable_in_course($courseid, $userid) {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/service/gradebookservices.php:860`
  ```php
  public static function find_ltiservice_gradebookservice_for_lti($instanceid) {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/service/gradebookservices.php:877`
  ```php
  public static function find_ltiservice_gradebookservice_for_lineitem($lineitemid) {
  ```
- `public/mod/lti/service/gradebookservices/classes/local/service/gradebookservices.php:892`
  ```php
  public static function validate_iso8601_date($date) {
  ```
- `public/mod/lti/service/gradebookservices/classes/privacy/provider.php:103`
  ```php
  public static function delete_data_for_users(approved_userlist $userlist) {
  ```
- `public/mod/lti/service/gradebookservices/classes/privacy/provider.php:111`
  ```php
  public static function delete_data_for_user(approved_contextlist $contextlist) {
  ```
- `public/mod/lti/service/gradebookservices/classes/privacy/provider.php:52`
  ```php
  public static function get_metadata(collection $collection): collection {
  ```
- `public/mod/lti/service/gradebookservices/classes/privacy/provider.php:70`
  ```php
  public static function get_contexts_for_userid(int $userid): contextlist {
  ```
- `public/mod/lti/service/gradebookservices/classes/privacy/provider.php:79`
  ```php
  public static function get_users_in_context(userlist $userlist) {
  ```
- `public/mod/lti/service/gradebookservices/classes/privacy/provider.php:87`
  ```php
  public static function export_user_data(approved_contextlist $contextlist) {
  ```
- `public/mod/lti/service/gradebookservices/classes/privacy/provider.php:95`
  ```php
  public static function delete_data_for_all_users_in_context(\context $context) {
  ```
- `public/mod/lti/service/gradebookservices/classes/task/cleanup_task.php:47`
  ```php
  public function get_name() {
  ```
- `public/mod/lti/service/gradebookservices/classes/task/cleanup_task.php:54`
  ```php
  public function execute() {
  ```
- `public/mod/lti/service/gradebookservices/db/upgrade.php:55`
  ```php
  function xmldb_ltiservice_gradebookservices_upgrade($oldversion) {
  ```
- `public/mod/lti/service/gradebookservices/tests/gradebookservices_test.php:110`
  ```php
  public function test_lti_add_standalone_lineitem(): void {
  ```
- `public/mod/lti/service/gradebookservices/tests/gradebookservices_test.php:132`
  ```php
  public function test_get_launch_parameters_coupled(): void {
  ```
- `public/mod/lti/service/gradebookservices/tests/gradebookservices_test.php:163`
  ```php
  public function test_get_launch_parameters_coupled_subreview_override(): void {
  ```
- `public/mod/lti/service/gradebookservices/tests/gradebookservices_test.php:191`
  ```php
  public function test_get_launch_parameters_coupled_subreview_override_default(): void {
  ```
- `public/mod/lti/service/gradebookservices/tests/gradebookservices_test.php:219`
  ```php
  public function test_get_launch_parameters_decoupled(): void {
  ```
- `public/mod/lti/service/gradebookservices/tests/gradebookservices_test.php:253`
  ```php
  public function test_is_user_gradable_in_course(): void {
  ```
- `public/mod/lti/service/gradebookservices/tests/gradebookservices_test.php:279`
  ```php
  private function assert_lineitems(object $course, int $typeid,
  ```
- `public/mod/lti/service/gradebookservices/tests/gradebookservices_test.php:339`
  ```php
  private function create_graded_lti(int $typeid, object $course, ?string $resourceid, ?string $tag,
  ```
- `public/mod/lti/service/gradebookservices/tests/gradebookservices_test.php:34`
  ```php
  public static function setUpBeforeClass(): void {
  ```
- `public/mod/lti/service/gradebookservices/tests/gradebookservices_test.php:362`
  ```php
  private function create_notgraded_lti(int $typeid, object $course): object {
  ```
- `public/mod/lti/service/gradebookservices/tests/gradebookservices_test.php:381`
  ```php
  private function create_standalone_lineitem(int $courseid, int $typeid, ?string $resourceid,
  ```
- `public/mod/lti/service/gradebookservices/tests/gradebookservices_test.php:398`
  ```php
  private function create_type() {
  ```
- `public/mod/lti/service/gradebookservices/tests/gradebookservices_test.php:47`
  ```php
  public function test_lti_add_coupled_lineitem(): void {
  ```
- `public/mod/lti/service/gradebookservices/tests/gradebookservices_test.php:82`
  ```php
  public function test_lti_add_coupled_lineitem_default_subreview(): void {
  ```
- `public/mod/lti/service/gradebookservices/tests/lineitem_test.php:135`
  ```php
  public function test_execute_put_addsubreview(): void {
  ```
- `public/mod/lti/service/gradebookservices/tests/lineitem_test.php:186`
  ```php
  public function test_sequential_score_posts(): void {
  ```
- `public/mod/lti/service/gradebookservices/tests/lineitem_test.php:284`
  ```php
  private function create_graded_lti(int $typeid, object $course, ?string $resourceid, ?string $tag,
  ```
- `public/mod/lti/service/gradebookservices/tests/lineitem_test.php:302`
  ```php
  private function create_type() {
  ```
- `public/mod/lti/service/gradebookservices/tests/lineitem_test.php:323`
  ```php
  private function set_server_for_put(object $course, int $typeid, object $lineitem) {
  ```
- `public/mod/lti/service/gradebookservices/tests/lineitem_test.php:40`
  ```php
  public function test_execute_put_nosubreview(): void {
  ```
- `public/mod/lti/service/gradebookservices/tests/lineitem_test.php:84`
  ```php
  public function test_execute_put_withsubreview(): void {
  ```
- `public/mod/lti/service/gradebookservices/tests/privacy/provider_test.php:40`
  ```php
  public function setUp(): void {
  ```
- `public/mod/lti/service/gradebookservices/tests/privacy/provider_test.php:48`
  ```php
  public function test_get_contexts_for_userid(): void {
  ```
- `public/mod/lti/service/gradebookservices/tests/task/cleanup_test.php:34`
  ```php
  public function setUp(): void {
  ```
- `public/mod/lti/service/gradebookservices/tests/task/cleanup_test.php:42`
  ```php
  public function test_cleanup_task(): void {
  ```
- `public/mod/lti/service/gradebookservices/tests/task/cleanup_test.php:99`
  ```php
  public function test_cleanup_task_with_manual_item(): void {
  ```
- `public/mod/lti/upgradelib.php:37`
  ```php
  function mod_lti_verify_private_key() {
  ```
- `public/mod/page/db/upgrade.php:45`
  ```php
  function xmldb_page_upgrade($oldversion) {
  ```
- `public/mod/qbank/db/upgrade.php:33`
  ```php
  function xmldb_qbank_upgrade($oldversion) {
  ```
- `public/mod/quiz/accessrule/seb/db/upgrade.php:36`
  ```php
  function xmldb_quizaccess_seb_upgrade($oldversion) {
  ```
- `public/mod/quiz/classes/admin/grade_method_setting.php:30`
  ```php
  public function load_choices() {
  ```
- `public/mod/quiz/classes/adminpresets/adminpresets_grade_method_setting.php:31`
  ```php
  public function set_behaviors() {
  ```
- `public/mod/quiz/classes/event/attempt_graded.php:39`
  ```php
  protected function init(): void {
  ```
- `public/mod/quiz/classes/event/attempt_graded.php:50`
  ```php
  public function get_description(): string {
  ```
- `public/mod/quiz/classes/event/attempt_graded.php:60`
  ```php
  public static function get_name(): string {
  ```
- `public/mod/quiz/classes/event/attempt_graded.php:69`
  ```php
  public function get_url(): \moodle_url {
  ```
- `public/mod/quiz/classes/event/attempt_graded.php:79`
  ```php
  protected function validate_data(): void {
  ```
- `public/mod/quiz/classes/event/attempt_graded.php:92`
  ```php
  public static function get_objectid_mapping(): array {
  ```
- `public/mod/quiz/classes/event/attempt_graded.php:97`
  ```php
  public static function get_other_mapping(): array {
  ```
- `public/mod/quiz/classes/event/attempt_regraded.php:107`
  ```php
  public static function get_objectid_mapping() {
  ```
- `public/mod/quiz/classes/event/attempt_regraded.php:116`
  ```php
  public static function get_other_mapping() {
  ```
- `public/mod/quiz/classes/event/attempt_regraded.php:46`
  ```php
  protected function init() {
  ```
- `public/mod/quiz/classes/event/attempt_regraded.php:57`
  ```php
  public static function get_name() {
  ```
- `public/mod/quiz/classes/event/attempt_regraded.php:66`
  ```php
  public function get_description() {
  ```
- `public/mod/quiz/classes/event/attempt_regraded.php:76`
  ```php
  public function get_url() {
  ```
- `public/mod/quiz/classes/event/attempt_regraded.php:86`
  ```php
  protected function validate_data() {
  ```
- `public/mod/quiz/classes/event/question_manually_graded.php:106`
  ```php
  public static function get_objectid_mapping() {
  ```
- `public/mod/quiz/classes/event/question_manually_graded.php:110`
  ```php
  public static function get_other_mapping() {
  ```
- `public/mod/quiz/classes/event/question_manually_graded.php:49`
  ```php
  protected function init() {
  ```
- `public/mod/quiz/classes/event/question_manually_graded.php:60`
  ```php
  public static function get_name() {
  ```
- `public/mod/quiz/classes/event/question_manually_graded.php:69`
  ```php
  public function get_description() {
  ```
- `public/mod/quiz/classes/event/question_manually_graded.php:79`
  ```php
  public function get_url() {
  ```
- `public/mod/quiz/classes/event/question_manually_graded.php:90`
  ```php
  protected function validate_data() {
  ```
- `public/mod/quiz/classes/event/quiz_grade_item_created.php:30`
  ```php
  protected function init() {
  ```
- `public/mod/quiz/classes/event/quiz_grade_item_created.php:36`
  ```php
  public static function get_name() {
  ```
- `public/mod/quiz/classes/event/quiz_grade_item_created.php:40`
  ```php
  public function get_description() {
  ```
- `public/mod/quiz/classes/event/quiz_grade_item_created.php:45`
  ```php
  public function get_url() {
  ```
- `public/mod/quiz/classes/event/quiz_grade_item_created.php:51`
  ```php
  protected function validate_data() {
  ```
- `public/mod/quiz/classes/event/quiz_grade_item_created.php:63`
  ```php
  public static function get_objectid_mapping() {
  ```
- `public/mod/quiz/classes/event/quiz_grade_item_created.php:67`
  ```php
  public static function get_other_mapping() {
  ```
- `public/mod/quiz/classes/event/quiz_grade_item_deleted.php:30`
  ```php
  protected function init() {
  ```
- `public/mod/quiz/classes/event/quiz_grade_item_deleted.php:36`
  ```php
  public static function get_name() {
  ```
- `public/mod/quiz/classes/event/quiz_grade_item_deleted.php:40`
  ```php
  public function get_description() {
  ```
- `public/mod/quiz/classes/event/quiz_grade_item_deleted.php:45`
  ```php
  public function get_url() {
  ```
- `public/mod/quiz/classes/event/quiz_grade_item_deleted.php:51`
  ```php
  protected function validate_data() {
  ```
- `public/mod/quiz/classes/event/quiz_grade_item_deleted.php:63`
  ```php
  public static function get_objectid_mapping() {
  ```
- `public/mod/quiz/classes/event/quiz_grade_item_deleted.php:67`
  ```php
  public static function get_other_mapping() {
  ```
- `public/mod/quiz/classes/event/quiz_grade_item_updated.php:30`
  ```php
  protected function init() {
  ```
- `public/mod/quiz/classes/event/quiz_grade_item_updated.php:36`
  ```php
  public static function get_name() {
  ```
- `public/mod/quiz/classes/event/quiz_grade_item_updated.php:40`
  ```php
  public function get_description() {
  ```
- `public/mod/quiz/classes/event/quiz_grade_item_updated.php:45`
  ```php
  public function get_url() {
  ```
- `public/mod/quiz/classes/event/quiz_grade_item_updated.php:51`
  ```php
  protected function validate_data() {
  ```
- `public/mod/quiz/classes/event/quiz_grade_item_updated.php:63`
  ```php
  public static function get_objectid_mapping() {
  ```
- `public/mod/quiz/classes/event/quiz_grade_item_updated.php:67`
  ```php
  public static function get_other_mapping() {
  ```
- `public/mod/quiz/classes/event/quiz_grade_items_reordered.php:30`
  ```php
  protected function init() {
  ```
- `public/mod/quiz/classes/event/quiz_grade_items_reordered.php:36`
  ```php
  public static function get_name() {
  ```
- `public/mod/quiz/classes/event/quiz_grade_items_reordered.php:40`
  ```php
  public function get_description() {
  ```
- `public/mod/quiz/classes/event/quiz_grade_items_reordered.php:45`
  ```php
  public function get_url() {
  ```
- `public/mod/quiz/classes/event/quiz_grade_items_reordered.php:51`
  ```php
  protected function validate_data() {
  ```
- `public/mod/quiz/classes/event/quiz_grade_items_reordered.php:59`
  ```php
  public static function get_objectid_mapping() {
  ```
- `public/mod/quiz/classes/event/quiz_grade_items_reordered.php:63`
  ```php
  public static function get_other_mapping() {
  ```
- `public/mod/quiz/classes/event/quiz_grade_updated.php:42`
  ```php
  protected function init() {
  ```
- `public/mod/quiz/classes/event/quiz_grade_updated.php:48`
  ```php
  public static function get_name() {
  ```
- `public/mod/quiz/classes/event/quiz_grade_updated.php:52`
  ```php
  public function get_description() {
  ```
- `public/mod/quiz/classes/event/quiz_grade_updated.php:58`
  ```php
  public function get_url() {
  ```
- `public/mod/quiz/classes/event/quiz_grade_updated.php:64`
  ```php
  protected function validate_data() {
  ```
- `public/mod/quiz/classes/event/quiz_grade_updated.php:84`
  ```php
  public static function get_objectid_mapping() {
  ```
- `public/mod/quiz/classes/event/quiz_grade_updated.php:88`
  ```php
  public static function get_other_mapping() {
  ```
- `public/mod/quiz/classes/event/slot_grade_item_updated.php:37`
  ```php
  protected function init() {
  ```
- `public/mod/quiz/classes/event/slot_grade_item_updated.php:43`
  ```php
  public static function get_name() {
  ```
- `public/mod/quiz/classes/event/slot_grade_item_updated.php:47`
  ```php
  public function get_description() {
  ```
- `public/mod/quiz/classes/event/slot_grade_item_updated.php:54`
  ```php
  public function get_url() {
  ```
- `public/mod/quiz/classes/event/slot_grade_item_updated.php:60`
  ```php
  protected function validate_data() {
  ```
- `public/mod/quiz/classes/event/slot_grade_item_updated.php:84`
  ```php
  public static function get_objectid_mapping() {
  ```
- `public/mod/quiz/classes/event/slot_grade_item_updated.php:88`
  ```php
  public static function get_other_mapping() {
  ```
- `public/mod/quiz/classes/external/create_grade_item_per_section.php:119`
  ```php
  public static function execute_returns(): ?external_description {
  ```
- `public/mod/quiz/classes/external/create_grade_item_per_section.php:49`
  ```php
  public static function execute_parameters(): external_function_parameters {
  ```
- `public/mod/quiz/classes/external/create_grade_item_per_section.php:64`
  ```php
  public static function execute(int $quizid): void {
  ```
- `public/mod/quiz/classes/external/create_grade_items.php:100`
  ```php
  public static function execute_returns(): ?external_description {
  ```
- `public/mod/quiz/classes/external/create_grade_items.php:47`
  ```php
  public static function execute_parameters(): external_function_parameters {
  ```
- `public/mod/quiz/classes/external/create_grade_items.php:67`
  ```php
  public static function execute(int $quizid, array $gradeitems): void {
  ```
- `public/mod/quiz/classes/external/delete_grade_items.php:48`
  ```php
  public static function execute_parameters(): external_function_parameters {
  ```
- `public/mod/quiz/classes/external/delete_grade_items.php:65`
  ```php
  public static function execute(int $quizid, array $gradeitems): void {
  ```
- `public/mod/quiz/classes/external/delete_grade_items.php:96`
  ```php
  public static function execute_returns(): ?external_description {
  ```
- `public/mod/quiz/classes/external/update_grade_items.php:102`
  ```php
  public static function execute_returns(): ?external_description {
  ```
- `public/mod/quiz/classes/external/update_grade_items.php:47`
  ```php
  public static function execute_parameters(): external_function_parameters {
  ```
- `public/mod/quiz/classes/external/update_grade_items.php:69`
  ```php
  public static function execute(int $quizid, array $gradeitems): void {
  ```
- `public/mod/quiz/classes/grade_calculator.php:124`
  ```php
  public function recompute_all_attempt_sumgrades(): void {
  ```
- `public/mod/quiz/classes/grade_calculator.php:157`
  ```php
  public function recompute_final_grade(?int $userid = null, array $attempts = []): void {
  ```
- `public/mod/quiz/classes/grade_calculator.php:202`
  ```php
  protected function compute_final_grade_from_attempts(array $attempts): ?float {
  ```
- `public/mod/quiz/classes/grade_calculator.php:249`
  ```php
  public function recompute_all_final_grades(): void {
  ```
- `public/mod/quiz/classes/grade_calculator.php:400`
  ```php
  public function update_quiz_maximum_grade(float $newgrade): void {
  ```
- `public/mod/quiz/classes/grade_calculator.php:468`
  ```php
  protected function ensure_grade_items_loaded(): void {
  ```
- `public/mod/quiz/classes/grade_calculator.php:499`
  ```php
  public function get_grade_items(): array {
  ```
- `public/mod/quiz/classes/grade_calculator.php:509`
  ```php
  public function set_slots(array $slots): void {
  ```
- `public/mod/quiz/classes/grade_calculator.php:527`
  ```php
  protected function ensure_slots_loaded(): void {
  ```
- `public/mod/quiz/classes/grade_calculator.php:545`
  ```php
  public function compute_grade_item_totals(question_usage_by_activity $quba): array {
  ```
- `public/mod/quiz/classes/grade_calculator.php:586`
  ```php
  public function compute_grade_item_totals_for_attempts(array $qubaids): array {
  ```
- `public/mod/quiz/classes/grade_calculator.php:622`
  ```php
  public function load_grade_item_totals(qubaid_condition $qubaids): array {
  ```
- `public/mod/quiz/classes/grade_calculator.php:71`
  ```php
  protected function __construct(quiz_settings $quizobj) {
  ```
- `public/mod/quiz/classes/grade_calculator.php:81`
  ```php
  public static function create(quiz_settings $quizobj): grade_calculator {
  ```
- `public/mod/quiz/classes/grade_calculator.php:93`
  ```php
  public function recompute_quiz_sumgrades(): void {
  ```
- `public/mod/quiz/classes/output/grades/grade_out_of.php:103`
  ```php
  public function style_formatted_values(stdClass $a): stdClass {
  ```
- `public/mod/quiz/classes/output/grades/grade_out_of.php:50`
  ```php
  public function __construct(
  ```
- `public/mod/quiz/classes/output/grades/grade_out_of.php:75`
  ```php
  public function get_string_key(): string {
  ```
- `public/mod/quiz/classes/output/grades/grade_out_of.php:86`
  ```php
  public function get_formatted_values(): stdClass {
  ```
- `public/mod/quiz/classes/task/grade_submission.php:40`
  ```php
  public static function instance(int $attemptid): self {
  ```
- `public/mod/quiz/classes/task/grade_submission.php:49`
  ```php
  public function execute(): void {
  ```
- `public/mod/quiz/db/upgrade.php:29`
  ```php
  function xmldb_quiz_upgrade($oldversion) {
  ```
- `public/mod/quiz/report/overview/db/upgrade.php:29`
  ```php
  function xmldb_quiz_overview_upgrade($oldversion) {
  ```
- `public/mod/quiz/report/statistics/db/upgrade.php:28`
  ```php
  function xmldb_quiz_statistics_upgrade($oldversion) {
  ```
- `public/mod/quiz/tests/backup/restore_quiz_grade_items_test.php:39`
  ```php
  public function test_restore_quiz_grade_items(): void {
  ```
- `public/mod/quiz/tests/external/grade_items_test.php:109`
  ```php
  public function test_cant_delete_grade_item_that_is_used(): void {
  ```
- `public/mod/quiz/tests/external/grade_items_test.php:119`
  ```php
  public function test_delete_grade_items_service_checks_permissions(): void {
  ```
- `public/mod/quiz/tests/external/grade_items_test.php:132`
  ```php
  public function test_get_edit_grading_page_data_service_works(): void {
  ```
- `public/mod/quiz/tests/external/grade_items_test.php:145`
  ```php
  public function test_get_edit_grading_page_data_service_checks_permissions(): void {
  ```
- `public/mod/quiz/tests/external/grade_items_test.php:160`
  ```php
  protected function create_quiz_with_two_grade_items(): quiz_settings {
  ```
- `public/mod/quiz/tests/external/grade_items_test.php:196`
  ```php
  public function test_create_grade_item_per_section_works(): void {
  ```
- `public/mod/quiz/tests/external/grade_items_test.php:244`
  ```php
  public function test_create_grade_item_per_section_with_descriptions(): void {
  ```
- `public/mod/quiz/tests/external/grade_items_test.php:290`
  ```php
  public function test_create_grade_item_per_section_service_checks_permissions(): void {
  ```
- `public/mod/quiz/tests/external/grade_items_test.php:306`
  ```php
  public function test_cant_create_grade_item_per_section_if_grade_items_already_exist(): void {
  ```
- `public/mod/quiz/tests/external/grade_items_test.php:37`
  ```php
  public function test_create_grade_items_service_works(): void {
  ```
- `public/mod/quiz/tests/external/grade_items_test.php:54`
  ```php
  public function test_create_grade_items_service_checks_permissions(): void {
  ```
- `public/mod/quiz/tests/external/grade_items_test.php:64`
  ```php
  public function test_update_grade_items_service_works(): void {
  ```
- `public/mod/quiz/tests/external/grade_items_test.php:82`
  ```php
  public function test_update_grade_items_service_checks_permissions(): void {
  ```
- `public/mod/quiz/tests/external/grade_items_test.php:92`
  ```php
  public function test_delete_grade_items_service_works(): void {
  ```
- `public/mod/quiz/tests/task/grade_submission_test.php:112`
  ```php
  public function test_attempt_state_finished(): void {
  ```
- `public/mod/quiz/tests/task/grade_submission_test.php:38`
  ```php
  private function create_attempt(): quiz_attempt {
  ```
- `public/mod/quiz/tests/task/grade_submission_test.php:63`
  ```php
  public function test_invalid_attempt(): void {
  ```
- `public/mod/quiz/tests/task/grade_submission_test.php:75`
  ```php
  public function test_attempt_state_inprogress(): void {
  ```
- `public/mod/quiz/tests/task/grade_submission_test.php:91`
  ```php
  public function test_attempt_state_submitted(): void {
  ```
- `public/mod/resource/db/upgrade.php:45`
  ```php
  function xmldb_resource_upgrade($oldversion) {
  ```
- `public/mod/scorm/classes/task/update_grades.php:30`
  ```php
  public function execute() {
  ```
- `public/mod/scorm/db/upgrade.php:30`
  ```php
  function xmldb_scorm_upgrade($oldversion) {
  ```
- `public/mod/subsection/db/upgrade.php:32`
  ```php
  function xmldb_subsection_upgrade($oldversion) {
  ```
- `public/mod/url/db/upgrade.php:45`
  ```php
  function xmldb_url_upgrade($oldversion) {
  ```
- `public/mod/wiki/db/upgrade.php:38`
  ```php
  function xmldb_wiki_upgrade($oldversion) {
  ```
- `public/mod/workshop/classes/grades/gradeitems.php:46`
  ```php
  public static function get_itemname_mapping_for_component(): array {
  ```
- `public/mod/workshop/classes/grades/gradeitems.php:63`
  ```php
  public static function get_field_name_for_itemnumber(string $component, int $itemnumber, string $fieldname): string {
  ```
- `public/mod/workshop/db/upgrade.php:35`
  ```php
  function xmldb_workshop_upgrade($oldversion) {
  ```
- `public/mod/workshop/form/accumulative/db/upgrade.php:31`
  ```php
  function xmldb_workshopform_accumulative_upgrade($oldversion) {
  ```
- `public/mod/workshop/form/comments/db/upgrade.php:31`
  ```php
  function xmldb_workshopform_comments_upgrade($oldversion) {
  ```
- `public/mod/workshop/form/numerrors/db/upgrade.php:31`
  ```php
  function xmldb_workshopform_numerrors_upgrade($oldversion) {
  ```
- `public/mod/workshop/form/rubric/db/upgrade.php:31`
  ```php
  function xmldb_workshopform_rubric_upgrade($oldversion) {
  ```
- `public/mod/zoom/db/upgrade.php:38`
  ```php
  function xmldb_zoom_upgrade($oldversion) {
  ```
- `public/mod/zoom/tests/mod_zoom_grade_test.php:114`
  ```php
  public function test_grade_type_not_none(): void {
  ```
- `public/mod/zoom/tests/mod_zoom_grade_test.php:145`
  ```php
  public function test_grade_delete(): void {
  ```
- `public/mod/zoom/tests/mod_zoom_grade_test.php:56`
  ```php
  public static function setUpBeforeClass(): void {
  ```
- `public/mod/zoom/tests/mod_zoom_grade_test.php:66`
  ```php
  public function setUp(): void {
  ```
- `public/mod/zoom/tests/mod_zoom_grade_test.php:81`
  ```php
  public function test_grade_added(): void {
  ```
- `public/payment/gateway/paypal/db/upgrade.php:31`
  ```php
  function xmldb_paygw_paypal_upgrade(int $oldversion): bool {
  ```
- `public/portfolio/googledocs/db/upgrade.php:21`
  ```php
  function xmldb_portfolio_googledocs_upgrade($oldversion) {
  ```
- `public/question/bank/columnsortorder/db/upgrade.php:33`
  ```php
  function xmldb_qbank_columnsortorder_upgrade(int $oldversion): bool {
  ```
- `public/question/bank/tagquestion/db/upgrade.php:31`
  ```php
  function xmldb_qbank_tagquestion_upgrade(int $oldversion): bool {
  ```
- `public/question/behaviour/manualgraded/behaviour.php:106`
  ```php
  public function process_finish(question_attempt_pending_step $pendingstep) {
  ```
- `public/question/behaviour/manualgraded/behaviour.php:42`
  ```php
  public function is_compatible_question(question_definition $question) {
  ```
- `public/question/behaviour/manualgraded/behaviour.php:46`
  ```php
  public function adjust_display_options(question_display_options $options) {
  ```
- `public/question/behaviour/manualgraded/behaviour.php:58`
  ```php
  public function process_action(question_attempt_pending_step $pendingstep) {
  ```
- `public/question/behaviour/manualgraded/behaviour.php:75`
  ```php
  public function process_save(question_attempt_pending_step $pendingstep) {
  ```
- `public/question/behaviour/manualgraded/behaviour.php:96`
  ```php
  public function summarise_action(question_attempt_step $step) {
  ```
- `public/question/behaviour/manualgraded/behaviourtype.php:36`
  ```php
  public function is_archetypal() {
  ```
- `public/question/behaviour/manualgraded/behaviourtype.php:40`
  ```php
  public function get_unused_display_options() {
  ```
- `public/question/behaviour/manualgraded/classes/privacy/provider.php:43`
  ```php
  public static function get_reason(): string {
  ```
- `public/question/behaviour/manualgraded/db/install.php:31`
  ```php
  function xmldb_qbehaviour_manualgraded_install() {
  ```
- `public/question/behaviour/manualgraded/db/upgrade.php:28`
  ```php
  function xmldb_qbehaviour_manualgraded_upgrade($oldversion) {
  ```
- `public/question/behaviour/manualgraded/tests/behaviour_type_test.php:41`
  ```php
  public function setUp(): void {
  ```
- `public/question/behaviour/manualgraded/tests/behaviour_type_test.php:46`
  ```php
  public function test_is_archetypal(): void {
  ```
- `public/question/behaviour/manualgraded/tests/behaviour_type_test.php:50`
  ```php
  public function test_get_unused_display_options(): void {
  ```
- `public/question/behaviour/manualgraded/tests/behaviour_type_test.php:55`
  ```php
  public function test_can_questions_finish_during_the_attempt(): void {
  ```
- `public/question/behaviour/manualgraded/tests/behaviour_type_test.php:59`
  ```php
  public function test_adjust_random_guess_score(): void {
  ```
- `public/question/behaviour/manualgraded/tests/walkthrough_test.php:110`
  ```php
  public function test_manual_graded_essay_not_answered(): void {
  ```
- `public/question/behaviour/manualgraded/tests/walkthrough_test.php:158`
  ```php
  public function test_manual_graded_truefalse(): void {
  ```
- `public/question/behaviour/manualgraded/tests/walkthrough_test.php:202`
  ```php
  public function test_manual_grade_ungraded_question(): void {
  ```
- `public/question/behaviour/manualgraded/tests/walkthrough_test.php:259`
  ```php
  public function test_manual_graded_ignore_repeat_sumbission(): void {
  ```
- `public/question/behaviour/manualgraded/tests/walkthrough_test.php:333`
  ```php
  public function test_manual_graded_ignore_repeat_sumbission_commas(): void {
  ```
- `public/question/behaviour/manualgraded/tests/walkthrough_test.php:37`
  ```php
  public function test_manual_graded_essay(): void {
  ```
- `public/question/behaviour/manualgraded/tests/walkthrough_test.php:381`
  ```php
  public function test_manual_graded_essay_can_grade_0(): void {
  ```
- `public/question/behaviour/manualgraded/tests/walkthrough_test.php:431`
  ```php
  public function test_manual_graded_change_comment_format(): void {
  ```
- `public/question/behaviour/manualgraded/tests/walkthrough_test.php:460`
  ```php
  public function test_manual_graded_respects_display_options(): void {
  ```
- `public/question/behaviour/manualgraded/tests/walkthrough_test.php:517`
  ```php
  public function test_manual_graded_invalid_value_throws_exception(): void {
  ```
- `public/question/behaviour/manualgraded/tests/walkthrough_test.php:564`
  ```php
  public function test_manual_graded_out_of_range_throws_exception(): void {
  ```
- `public/question/behaviour/manualgraded/tests/walkthrough_test.php:611`
  ```php
  public function test_manual_graded_displays_proper_comment_format(): void {
  ```
- `public/question/behaviour/manualgraded/tests/walkthrough_test.php:650`
  ```php
  public function test_manual_grading_reshows_exactly_the_mark_input(): void {
  ```
- `public/question/behaviour/manualgraded/tests/walkthrough_test.php:686`
  ```php
  public function test_manual_grading_history_display(): void {
  ```
- `public/question/engine/upgrade/upgradelib.php:109`
  ```php
  protected function set_quba_preferred_behaviour($qubaid, $preferredbehaviour) {
  ```
- `public/question/engine/upgrade/upgradelib.php:115`
  ```php
  protected function set_quiz_attempt_layout($qubaid, $layout) {
  ```
- `public/question/engine/upgrade/upgradelib.php:120`
  ```php
  protected function delete_quiz_attempt($qubaid) {
  ```
- `public/question/engine/upgrade/upgradelib.php:126`
  ```php
  protected function insert_record($table, $record, $saveid = true) {
  ```
- `public/question/engine/upgrade/upgradelib.php:135`
  ```php
  public function load_question($questionid, $quizid = null) {
  ```
- `public/question/engine/upgrade/upgradelib.php:139`
  ```php
  public function load_dataset($questionid, $selecteditem) {
  ```
- `public/question/engine/upgrade/upgradelib.php:143`
  ```php
  public function get_next_question_session($attempt, moodle_recordset $questionsessionsrs) {
  ```
- `public/question/engine/upgrade/upgradelib.php:159`
  ```php
  public function get_question_states($attempt, $question, moodle_recordset $questionsstatesrs) {
  ```
- `public/question/engine/upgrade/upgradelib.php:178`
  ```php
  protected function get_converter_class_name($question, $quiz, $qsessionid) {
  ```
- `public/question/engine/upgrade/upgradelib.php:205`
  ```php
  public function supply_missing_question_attempt($quiz, $attempt, $question) {
  ```
- `public/question/engine/upgrade/upgradelib.php:220`
  ```php
  public function convert_question_attempt($quiz, $attempt, $question, $qsession, $qstates) {
  ```
- `public/question/engine/upgrade/upgradelib.php:236`
  ```php
  protected function decode_random_attempt($qstates, $maxmark) {
  ```
- `public/question/engine/upgrade/upgradelib.php:267`
  ```php
  public function prepare_to_restore() {
  ```
- `public/question/engine/upgrade/upgradelib.php:288`
  ```php
  public function __construct($logger) {
  ```
- `public/question/engine/upgrade/upgradelib.php:292`
  ```php
  protected function load_question($questionid, $quizid) {
  ```
- `public/question/engine/upgrade/upgradelib.php:331`
  ```php
  public function get_question($questionid, $quizid) {
  ```
- `public/question/engine/upgrade/upgradelib.php:352`
  ```php
  public function load_dataset($questionid, $selecteditem) {
  ```
- `public/question/engine/upgrade/upgradelib.php:389`
  ```php
  public function __construct($updater, $question, $logger, $qeupdater) {
  ```
- `public/question/engine/upgrade/upgradelib.php:396`
  ```php
  public function discard() {
  ```
- `public/question/engine/upgrade/upgradelib.php:404`
  ```php
  protected function to_text($html) {
  ```
- `public/question/engine/upgrade/upgradelib.php:408`
  ```php
  public function question_summary() {
  ```
- `public/question/engine/upgrade/upgradelib.php:412`
  ```php
  public function compare_answers($answer1, $answer2) {
  ```
- `public/question/engine/upgrade/upgradelib.php:416`
  ```php
  public function is_blank_answer($state) {
  ```
- `public/question/engine/upgrade/upgradelib.php:430`
  ```php
  public function right_answer() {
  ```
- `public/question/engine/upgrade/upgradelib.php:434`
  ```php
  public function response_summary($state) {
  ```
- `public/question/engine/upgrade/upgradelib.php:438`
  ```php
  public function was_answered($state) {
  ```
- `public/question/engine/upgrade/upgradelib.php:442`
  ```php
  public function set_first_step_data_elements($state, &$data) {
  ```
- `public/question/engine/upgrade/upgradelib.php:446`
  ```php
  public function supply_missing_first_step_data(&$data) {
  ```
- `public/question/engine/upgrade/upgradelib.php:449`
  ```php
  public function set_data_elements_for_step($state, &$data) {
  ```
- `public/question/engine/upgrade/upgradelib.php:464`
  ```php
  function quiz_attempts_upgraded(environment_results $result) {
  ```
- `public/question/engine/upgrade/upgradelib.php:51`
  ```php
  public function save_usage($preferredbehaviour, $attempt, $qas, $quizlayout) {
  ```
- `public/question/type/calculated/db/upgrade.php:30`
  ```php
  function xmldb_qtype_calculated_upgrade($oldversion) {
  ```
- `public/question/type/calculated/db/upgradelib.php:102`
  ```php
  public function response_summary($state) {
  ```
- `public/question/type/calculated/db/upgradelib.php:122`
  ```php
  public function was_answered($state) {
  ```
- `public/question/type/calculated/db/upgradelib.php:126`
  ```php
  public function set_first_step_data_elements($state, &$data) {
  ```
- `public/question/type/calculated/db/upgradelib.php:139`
  ```php
  public function supply_missing_first_step_data(&$data) {
  ```
- `public/question/type/calculated/db/upgradelib.php:143`
  ```php
  public function set_data_elements_for_step($state, &$data) {
  ```
- `public/question/type/calculated/db/upgradelib.php:171`
  ```php
  public function load_dataset($selecteditem) {
  ```
- `public/question/type/calculated/db/upgradelib.php:211`
  ```php
  public function format_float($x, $length = null, $format = null) {
  ```
- `public/question/type/calculated/db/upgradelib.php:230`
  ```php
  public function calculate($expression) {
  ```
- `public/question/type/calculated/db/upgradelib.php:240`
  ```php
  protected function calculate_raw($expression) {
  ```
- `public/question/type/calculated/db/upgradelib.php:262`
  ```php
  protected function substitute_values_for_eval($expression) {
  ```
- `public/question/type/calculated/db/upgradelib.php:274`
  ```php
  protected function substitute_values_pretty($text) {
  ```
- `public/question/type/calculated/db/upgradelib.php:284`
  ```php
  public function replace_expressions_in_text($text, $length = null, $format = null) {
  ```
- `public/question/type/calculated/db/upgradelib.php:47`
  ```php
  public function question_summary() {
  ```
- `public/question/type/calculated/db/upgradelib.php:51`
  ```php
  public function right_answer() {
  ```
- `public/question/type/calculated/db/upgradelib.php:71`
  ```php
  protected function parse_response($state) {
  ```
- `public/question/type/calculated/tests/upgrade_old_attempt_data_test.php:231`
  ```php
  public function test_calculated_adaptive_qsession100(): void {
  ```
- `public/question/type/calculated/tests/upgrade_old_attempt_data_test.php:33`
  ```php
  public function test_calculated_adaptive_qsession97(): void {
  ```
- `public/question/type/calculated/tests/upgrade_old_attempt_data_test.php:472`
  ```php
  public function test_calculated_adaptive_qsession103(): void {
  ```
- `public/question/type/calculatedmulti/db/upgrade.php:30`
  ```php
  function xmldb_qtype_calculatedmulti_upgrade($oldversion) {
  ```
- `public/question/type/calculatedmulti/db/upgradelib.php:100`
  ```php
  public function response_summary($state) {
  ```
- `public/question/type/calculatedmulti/db/upgradelib.php:140`
  ```php
  public function was_answered($state) {
  ```
- `public/question/type/calculatedmulti/db/upgradelib.php:149`
  ```php
  public function set_first_step_data_elements($state, &$data) {
  ```
- `public/question/type/calculatedmulti/db/upgradelib.php:166`
  ```php
  public function supply_missing_first_step_data(&$data) {
  ```
- `public/question/type/calculatedmulti/db/upgradelib.php:170`
  ```php
  public function set_data_elements_for_step($state, &$data) {
  ```
- `public/question/type/calculatedmulti/db/upgradelib.php:193`
  ```php
  public function load_dataset($selecteditem) {
  ```
- `public/question/type/calculatedmulti/db/upgradelib.php:235`
  ```php
  public function format_float($x, $length = null, $format = null) {
  ```
- `public/question/type/calculatedmulti/db/upgradelib.php:254`
  ```php
  public function calculate($expression) {
  ```
- `public/question/type/calculatedmulti/db/upgradelib.php:264`
  ```php
  protected function calculate_raw($expression) {
  ```
- `public/question/type/calculatedmulti/db/upgradelib.php:286`
  ```php
  protected function substitute_values_for_eval($expression) {
  ```
- `public/question/type/calculatedmulti/db/upgradelib.php:298`
  ```php
  protected function substitute_values_pretty($text) {
  ```
- `public/question/type/calculatedmulti/db/upgradelib.php:308`
  ```php
  public function replace_expressions_in_text($text, $length = null, $format = null) {
  ```
- `public/question/type/calculatedmulti/db/upgradelib.php:49`
  ```php
  public function question_summary() {
  ```
- `public/question/type/calculatedmulti/db/upgradelib.php:53`
  ```php
  public function right_answer() {
  ```
- `public/question/type/calculatedmulti/db/upgradelib.php:71`
  ```php
  protected function explode_answer($state) {
  ```
- `public/question/type/calculatedmulti/tests/upgrade_old_attempt_data_test.php:253`
  ```php
  public function test_calculatedmulti_adaptive_qsession99(): void {
  ```
- `public/question/type/calculatedmulti/tests/upgrade_old_attempt_data_test.php:33`
  ```php
  public function test_calculatedmulti_adaptive_qsession96(): void {
  ```
- `public/question/type/calculatedmulti/tests/upgrade_old_attempt_data_test.php:493`
  ```php
  public function test_calculatedmulti_adaptive_qsession102(): void {
  ```
- `public/question/type/calculatedsimple/tests/upgrade_old_attempt_data_test.php:218`
  ```php
  public function test_calculatedsimple_adaptive_qsession98(): void {
  ```
- `public/question/type/calculatedsimple/tests/upgrade_old_attempt_data_test.php:33`
  ```php
  public function test_calculatedsimple_adaptive_qsession95(): void {
  ```
- `public/question/type/calculatedsimple/tests/upgrade_old_attempt_data_test.php:443`
  ```php
  public function test_calculatedsimple_adaptive_qsession101(): void {
  ```
- `public/question/type/ddimageortext/db/upgrade.php:30`
  ```php
  function xmldb_qtype_ddimageortext_upgrade($oldversion) {
  ```
- `public/question/type/ddmarker/db/upgrade.php:30`
  ```php
  function xmldb_qtype_ddmarker_upgrade($oldversion) {
  ```
- `public/question/type/description/db/upgradelib.php:40`
  ```php
  public function right_answer() {
  ```
- `public/question/type/description/db/upgradelib.php:44`
  ```php
  public function was_answered($state) {
  ```
- `public/question/type/description/db/upgradelib.php:48`
  ```php
  public function response_summary($state) {
  ```
- `public/question/type/description/db/upgradelib.php:52`
  ```php
  public function set_first_step_data_elements($state, &$data) {
  ```
- `public/question/type/description/db/upgradelib.php:55`
  ```php
  public function supply_missing_first_step_data(&$data) {
  ```
- `public/question/type/description/db/upgradelib.php:58`
  ```php
  public function set_data_elements_for_step($state, &$data) {
  ```
- `public/question/type/description/tests/upgrade_old_attempt_data_test.php:179`
  ```php
  public function test_description_deferredfeedback_history70(): void {
  ```
- `public/question/type/description/tests/upgrade_old_attempt_data_test.php:324`
  ```php
  public function test_description_deferredfeedback_history0(): void {
  ```
- `public/question/type/description/tests/upgrade_old_attempt_data_test.php:34`
  ```php
  public function test_description_deferredfeedback_history80(): void {
  ```
- `public/question/type/essay/db/upgrade.php:30`
  ```php
  function xmldb_qtype_essay_upgrade($oldversion) {
  ```
- `public/question/type/essay/db/upgradelib.php:40`
  ```php
  public function right_answer() {
  ```
- `public/question/type/essay/db/upgradelib.php:44`
  ```php
  public function response_summary($state) {
  ```
- `public/question/type/essay/db/upgradelib.php:52`
  ```php
  public function was_answered($state) {
  ```
- `public/question/type/essay/db/upgradelib.php:56`
  ```php
  public function set_first_step_data_elements($state, &$data) {
  ```
- `public/question/type/essay/db/upgradelib.php:59`
  ```php
  public function supply_missing_first_step_data(&$data) {
  ```
- `public/question/type/essay/db/upgradelib.php:62`
  ```php
  public function set_data_elements_for_step($state, &$data) {
  ```
- `public/question/type/essay/tests/upgrade_old_attempt_data_test.php:267`
  ```php
  public function test_essay_deferredfeedback_history820(): void {
  ```
- `public/question/type/essay/tests/upgrade_old_attempt_data_test.php:34`
  ```php
  public function test_essay_deferredfeedback_history98220(): void {
  ```
- `public/question/type/essay/tests/upgrade_old_attempt_data_test.php:446`
  ```php
  public function test_essay_deferredfeedback_missing(): void {
  ```
- `public/question/type/match/db/upgrade.php:29`
  ```php
  function xmldb_qtype_match_upgrade($oldversion) {
  ```
- `public/question/type/match/db/upgradelib.php:111`
  ```php
  public function response_summary($state) {
  ```
- `public/question/type/match/db/upgradelib.php:139`
  ```php
  public function was_answered($state) {
  ```
- `public/question/type/match/db/upgradelib.php:149`
  ```php
  public function set_first_step_data_elements($state, &$data) {
  ```
- `public/question/type/match/db/upgradelib.php:166`
  ```php
  public function supply_missing_first_step_data(&$data) {
  ```
- `public/question/type/match/db/upgradelib.php:173`
  ```php
  public function set_data_elements_for_step($state, &$data) {
  ```
- `public/question/type/match/db/upgradelib.php:46`
  ```php
  public function question_summary() {
  ```
- `public/question/type/match/db/upgradelib.php:69`
  ```php
  public function right_answer() {
  ```
- `public/question/type/match/db/upgradelib.php:77`
  ```php
  protected function explode_answer($answer) {
  ```
- `public/question/type/match/db/upgradelib.php:90`
  ```php
  protected function make_summary($pairs) {
  ```
- `public/question/type/match/db/upgradelib.php:98`
  ```php
  protected function lookup_choice($choice) {
  ```
- `public/question/type/match/tests/upgrade_old_attempt_data_test.php:290`
  ```php
  public function test_match_deferredfeedback_history60(): void {
  ```
- `public/question/type/match/tests/upgrade_old_attempt_data_test.php:34`
  ```php
  public function test_match_deferredfeedback_history6220(): void {
  ```
- `public/question/type/match/tests/upgrade_old_attempt_data_test.php:500`
  ```php
  public function test_match_deferredfeedback_history622220(): void {
  ```
- `public/question/type/multianswer/db/upgrade.php:30`
  ```php
  function xmldb_qtype_multianswer_upgrade($oldversion) {
  ```
- `public/question/type/multianswer/db/upgradelib.php:103`
  ```php
  public function response_summary($state) {
  ```
- `public/question/type/multianswer/db/upgradelib.php:113`
  ```php
  public function was_answered($state) {
  ```
- `public/question/type/multianswer/db/upgradelib.php:117`
  ```php
  public function set_first_step_data_elements($state, &$data) {
  ```
- `public/question/type/multianswer/db/upgradelib.php:131`
  ```php
  public function supply_missing_first_step_data(&$data) {
  ```
- `public/question/type/multianswer/db/upgradelib.php:134`
  ```php
  public function set_data_elements_for_step($state, &$data) {
  ```
- `public/question/type/multianswer/db/upgradelib.php:161`
  ```php
  public function add_prefix($field, $i) {
  ```
- `public/question/type/multianswer/db/upgradelib.php:41`
  ```php
  public function question_summary() {
  ```
- `public/question/type/multianswer/db/upgradelib.php:64`
  ```php
  public function right_answer() {
  ```
- `public/question/type/multianswer/db/upgradelib.php:79`
  ```php
  public function explode_answer($answer) {
  ```
- `public/question/type/multianswer/db/upgradelib.php:91`
  ```php
  public function display_response($response) {
  ```
- `public/question/type/multianswer/tests/upgrade_old_attempt_data_test.php:1327`
  ```php
  public function test_multianswer_adaptivenopenalty_qsession107(): void {
  ```
- `public/question/type/multianswer/tests/upgrade_old_attempt_data_test.php:1984`
  ```php
  public function test_multianswer_adaptivenopenalty_qsession109(): void {
  ```
- `public/question/type/multianswer/tests/upgrade_old_attempt_data_test.php:236`
  ```php
  public function test_multianswer_adaptivenopenalty_qsession106(): void {
  ```
- `public/question/type/multianswer/tests/upgrade_old_attempt_data_test.php:33`
  ```php
  public function test_multianswer_adaptivenopenalty_qsession104(): void {
  ```
- `public/question/type/multianswer/tests/upgrade_old_attempt_data_test.php:418`
  ```php
  public function test_multianswer_adaptivenopenalty_qsession108(): void {
  ```
- `public/question/type/multianswer/tests/upgrade_old_attempt_data_test.php:642`
  ```php
  public function test_multianswer_adaptivenopenalty_qsession105(): void {
  ```
- `public/question/type/multichoice/db/upgrade.php:30`
  ```php
  function xmldb_qtype_multichoice_upgrade($oldversion) {
  ```
- `public/question/type/multichoice/db/upgradelib.php:117`
  ```php
  public function was_answered($state) {
  ```
- `public/question/type/multichoice/db/upgradelib.php:126`
  ```php
  public function set_first_step_data_elements($state, &$data) {
  ```
- `public/question/type/multichoice/db/upgradelib.php:135`
  ```php
  public function supply_missing_first_step_data(&$data) {
  ```
- `public/question/type/multichoice/db/upgradelib.php:139`
  ```php
  public function set_data_elements_for_step($state, &$data) {
  ```
- `public/question/type/multichoice/db/upgradelib.php:42`
  ```php
  public function is_blank_answer($state) {
  ```
- `public/question/type/multichoice/db/upgradelib.php:47`
  ```php
  public function right_answer() {
  ```
- `public/question/type/multichoice/db/upgradelib.php:66`
  ```php
  protected function explode_answer($answer) {
  ```
- `public/question/type/multichoice/db/upgradelib.php:79`
  ```php
  public function response_summary($state) {
  ```
- `public/question/type/multichoice/tests/upgrade_old_attempt_data_test.php:1001`
  ```php
  public function test_multichoice_deferredfeedback_qsession140(): void {
  ```
- `public/question/type/multichoice/tests/upgrade_old_attempt_data_test.php:1203`
  ```php
  public function test_multichoice_deferredfeedback_qsession2018195(): void {
  ```
- `public/question/type/multichoice/tests/upgrade_old_attempt_data_test.php:1449`
  ```php
  public function test_multichoice_deferredfeedback_qsession2653368(): void {
  ```
- `public/question/type/multichoice/tests/upgrade_old_attempt_data_test.php:1674`
  ```php
  public function test_multichoice_deferredfeedback_qsession3131(): void {
  ```
- `public/question/type/multichoice/tests/upgrade_old_attempt_data_test.php:1919`
  ```php
  public function test_multichoice_deferredfeedback_qsession4307870(): void {
  ```
- `public/question/type/multichoice/tests/upgrade_old_attempt_data_test.php:2124`
  ```php
  public function test_multichoice_deferredfeedback_qsession49446(): void {
  ```
- `public/question/type/multichoice/tests/upgrade_old_attempt_data_test.php:2339`
  ```php
  public function test_multichoice_deferredfeedback_qsession591(): void {
  ```
- `public/question/type/multichoice/tests/upgrade_old_attempt_data_test.php:2548`
  ```php
  public function test_multichoice_deferredfeedback_qsession594(): void {
  ```
- `public/question/type/multichoice/tests/upgrade_old_attempt_data_test.php:265`
  ```php
  public function test_multichoice_deferredfeedback_history0(): void {
  ```
- `public/question/type/multichoice/tests/upgrade_old_attempt_data_test.php:33`
  ```php
  public function test_multichoice_deferredfeedback_history960(): void {
  ```
- `public/question/type/multichoice/tests/upgrade_old_attempt_data_test.php:425`
  ```php
  public function test_multichoice_deferredfeedback_history60(): void {
  ```
- `public/question/type/multichoice/tests/upgrade_old_attempt_data_test.php:628`
  ```php
  public function test_multichoice_deferredfeedback_history6220(): void {
  ```
- `public/question/type/multichoice/tests/upgrade_old_attempt_data_test.php:867`
  ```php
  public function test_multichoice_deferredfeedback_missing(): void {
  ```
- `public/question/type/numerical/db/upgrade.php:30`
  ```php
  function xmldb_qtype_numerical_upgrade($oldversion) {
  ```
- `public/question/type/numerical/db/upgradelib.php:40`
  ```php
  public function right_answer() {
  ```
- `public/question/type/numerical/db/upgradelib.php:60`
  ```php
  public function response_summary($state) {
  ```
- `public/question/type/numerical/db/upgradelib.php:85`
  ```php
  public function was_answered($state) {
  ```
- `public/question/type/numerical/db/upgradelib.php:89`
  ```php
  public function set_first_step_data_elements($state, &$data) {
  ```
- `public/question/type/numerical/db/upgradelib.php:93`
  ```php
  public function supply_missing_first_step_data(&$data) {
  ```
- `public/question/type/numerical/db/upgradelib.php:97`
  ```php
  public function set_data_elements_for_step($state, &$data) {
  ```
- `public/question/type/numerical/tests/upgrade_old_attempt_data_test.php:259`
  ```php
  public function test_numerical_deferredfeedback_required_units(): void {
  ```
- `public/question/type/numerical/tests/upgrade_old_attempt_data_test.php:34`
  ```php
  public function test_numerical_deferredfeedback_history620(): void {
  ```
- `public/question/type/ordering/classes/output/specific_grade_detail_feedback.php:29`
  ```php
  public function export_for_template(\renderer_base $output): array {
  ```
- `public/question/type/ordering/db/upgrade.php:30`
  ```php
  function xmldb_qtype_ordering_upgrade($oldversion) {
  ```
- `public/question/type/ordering/tests/output/specific_grade_detail_feedback_test.php:50`
  ```php
  public function test_export_for_template(array $answeritems, int $gradingtype, string $layouttype, array $expected,
  ```
- `public/question/type/ordering/tests/output/specific_grade_detail_feedback_test.php:97`
  ```php
  public static function export_for_template_provider(): array {
  ```
- `public/question/type/randomsamatch/db/upgrade.php:29`
  ```php
  function xmldb_qtype_randomsamatch_upgrade($oldversion) {
  ```
- `public/question/type/randomsamatch/db/upgradelib.php:116`
  ```php
  public function response_summary($state) {
  ```
- `public/question/type/randomsamatch/db/upgradelib.php:144`
  ```php
  public function was_answered($state) {
  ```
- `public/question/type/randomsamatch/db/upgradelib.php:154`
  ```php
  public function set_first_step_data_elements($state, &$data) {
  ```
- `public/question/type/randomsamatch/db/upgradelib.php:217`
  ```php
  public function supply_missing_first_step_data(&$data) {
  ```
- `public/question/type/randomsamatch/db/upgradelib.php:224`
  ```php
  public function set_data_elements_for_step($state, &$data) {
  ```
- `public/question/type/randomsamatch/db/upgradelib.php:242`
  ```php
  public function load_question($questionid) {
  ```
- `public/question/type/randomsamatch/db/upgradelib.php:58`
  ```php
  public function question_summary() {
  ```
- `public/question/type/randomsamatch/db/upgradelib.php:62`
  ```php
  public function right_answer() {
  ```
- `public/question/type/randomsamatch/db/upgradelib.php:72`
  ```php
  protected function explode_answer($answer) {
  ```
- `public/question/type/randomsamatch/db/upgradelib.php:85`
  ```php
  protected function make_summary($pairs) {
  ```
- `public/question/type/randomsamatch/db/upgradelib.php:99`
  ```php
  protected function lookup_choice($choice) {
  ```
- `public/question/type/randomsamatch/tests/upgrade_old_attempt_data_test.php:33`
  ```php
  public function test_randomsamatch_deferredfeedback_qsession1(): void {
  ```
- `public/question/type/shortanswer/db/upgrade.php:30`
  ```php
  function xmldb_qtype_shortanswer_upgrade($oldversion) {
  ```
- `public/question/type/shortanswer/db/upgradelib.php:40`
  ```php
  public function right_answer() {
  ```
- `public/question/type/shortanswer/db/upgradelib.php:48`
  ```php
  public function was_answered($state) {
  ```
- `public/question/type/shortanswer/db/upgradelib.php:52`
  ```php
  public function response_summary($state) {
  ```
- `public/question/type/shortanswer/db/upgradelib.php:60`
  ```php
  public function set_first_step_data_elements($state, &$data) {
  ```
- `public/question/type/shortanswer/db/upgradelib.php:63`
  ```php
  public function supply_missing_first_step_data(&$data) {
  ```
- `public/question/type/shortanswer/db/upgradelib.php:66`
  ```php
  public function set_data_elements_for_step($state, &$data) {
  ```
- `public/question/type/shortanswer/tests/upgrade_old_attempt_data_test.php:243`
  ```php
  public function test_shortanswer_deferredfeedback_history60(): void {
  ```
- `public/question/type/shortanswer/tests/upgrade_old_attempt_data_test.php:34`
  ```php
  public function test_shortanswer_deferredfeedback_history620(): void {
  ```
- `public/question/type/shortanswer/tests/upgrade_old_attempt_data_test.php:416`
  ```php
  public function test_shortanswer_deferredfeedback_history3220(): void {
  ```
- `public/question/type/truefalse/db/upgrade.php:31`
  ```php
  function xmldb_qtype_truefalse_upgrade(int $oldversion): bool {
  ```
- `public/question/type/truefalse/db/upgradelib.php:40`
  ```php
  public function right_answer() {
  ```
- `public/question/type/truefalse/db/upgradelib.php:48`
  ```php
  public function response_summary($state) {
  ```
- `public/question/type/truefalse/db/upgradelib.php:63`
  ```php
  public function was_answered($state) {
  ```
- `public/question/type/truefalse/db/upgradelib.php:67`
  ```php
  public function set_first_step_data_elements($state, &$data) {
  ```
- `public/question/type/truefalse/db/upgradelib.php:70`
  ```php
  public function supply_missing_first_step_data(&$data) {
  ```
- `public/question/type/truefalse/db/upgradelib.php:73`
  ```php
  public function set_data_elements_for_step($state, &$data) {
  ```
- `public/question/type/truefalse/tests/upgrade_old_attempt_data_test.php:223`
  ```php
  public function test_truefalse_deferredfeedback_history20(): void {
  ```
- `public/question/type/truefalse/tests/upgrade_old_attempt_data_test.php:34`
  ```php
  public function test_truefalse_deferredfeedback_history620(): void {
  ```
- `public/question/type/truefalse/tests/upgrade_old_attempt_data_test.php:401`
  ```php
  public function test_truefalse_deferredfeedback_history90(): void {
  ```
- `public/question/type/truefalse/tests/upgrade_old_attempt_data_test.php:577`
  ```php
  public function test_truefalse_adaptive_qsession119(): void {
  ```
- `public/question/type/truefalse/tests/upgrade_old_attempt_data_test.php:767`
  ```php
  public function test_truefalse_adaptive_qsession120(): void {
  ```
- `public/question/type/truefalse/tests/upgrade_old_attempt_data_test.php:956`
  ```php
  public function test_truefalse_adaptive_qsession3(): void {
  ```
- `public/repository/dropbox/db/upgrade.php:21`
  ```php
  function xmldb_repository_dropbox_upgrade($oldversion) {
  ```
- `public/repository/flickr/db/upgrade.php:32`
  ```php
  function xmldb_repository_flickr_upgrade($oldversion) {
  ```
- `public/repository/googledocs/db/upgrade.php:21`
  ```php
  function xmldb_repository_googledocs_upgrade($oldversion) {
  ```
- `public/repository/onedrive/db/upgrade.php:24`
  ```php
  function xmldb_repository_onedrive_upgrade($oldversion) {
  ```
- `public/search/engine/simpledb/db/upgrade.php:29`
  ```php
  function xmldb_search_simpledb_upgrade($oldversion = 0) {
  ```
- `public/theme/classic/tests/behat/behat_theme_classic_behat_grade.php:47`
  ```php
  public function i_navigate_to_in_the_course_gradebook($gradepath) {
  ```
- `public/theme/moove/db/upgrade.php:32`
  ```php
  function xmldb_theme_moove_upgrade($oldversion = 0) {
  ```
- `public/user/profile/field/social/upgradelib.php:150`
  ```php
  function user_profile_social_update_module_availability() {
  ```
- `public/user/profile/field/social/upgradelib.php:178`
  ```php
  function user_profile_social_update_availability_structure(&$structure) {
  ```
- `public/user/profile/field/social/upgradelib.php:34`
  ```php
  function user_profile_social_create_info_category(): int {
  ```
- `public/user/profile/field/social/upgradelib.php:56`
  ```php
  function user_profile_social_moveto_profilefield($social) {
  ```
- `public/user/profile/field/social/upgradelib.php:82`
  ```php
  function user_profile_social_create_profilefield($social) {
  ```
