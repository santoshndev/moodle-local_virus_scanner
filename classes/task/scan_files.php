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
 * Scheduled Task
 *
 * @package     local_virus_scanner
 * @category    task
 * @copyright   2022 Santosh N. <santosh.nag2217@gmail.com>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace local_virus_scanner\task;

class scan_files extends \core\task\scheduled_task {

    /**
     * name of the task
     */
    public function get_name() {
        return get_string('scanfiles', 'local_virus_scanner');
    }

    /**
     * run the task
     */
    public function execute() {
        global $CFG, $DB;

        $yesterday = time() - 60 * 60 * 24;   //get yesterday
        $today = time();  //get today

        $select = $DB->sql_equal('filearea', ':smfile') . " AND " . $DB->sql_equal('filename', ':fname', true, true, true);
        $select .= " AND (timecreated < :today AND timecreated > :yesterday)";
        $select .= " OR (timemodified < :today1 AND timemodified > :yesterday1)";
        $params = [
                'smfile' => 'submission_files',
                'fname' => '.',
                'today' => $today,
                'yesterday' => $yesterday,
                'today1' => $today,
                'yesterday1' => $yesterday
        ];
        //Get the list of files uploaded yesterday
        $files = $DB->get_records_select('files', $select, $params);
        $data = array();
        $data[] = ['filename', 'filepath', 'uploadedby', 'filetype', 'uploadedon'];
        // Scan each and every file.
        foreach ($files as $filee) {
            $chash = $filee->contenthash;
            $parent = substr($chash, 0, 2);
            $child = substr($chash, 2, 2);
            $filepath = $CFG->dataroot . '/filedir/' . $parent . '/' . $child . '/' . $chash;

            $cmd = get_config('local_virus_scanner', 'clamav') . ' ' . $filepath;

            exec($cmd, $output, $return);

            if ($return) {
                $data[] = [$filee->filename, $filepath, $filee->author, $filee->mimetype, userdate($filee->timecreated)];
            }

        }
        // Create csv file of infected file details.
        $dirname = get_config('local_virus_scanner', 'directory');
        $filename = 'virus_infection_report_' . userdate(time(), '%Y%m%d') . '.csv';
        $filepath1 = $CFG->dataroot;
        $filelocation = $filepath1 . '/' . $dirname . '/' . $filename;
        // Open csv file for writing.
        $f = fopen($filelocation, 'w');

        if ($f === false) {
            die('Error opening the file ' . $filename);
        }

        // Write each row at a time to a file.
        foreach ($data as $row) {
            fputcsv($f, $row);
        }

        // Close the file.
        fclose($f);

        $linecount = count(file($filelocation));

        // Send mail to admin.
        if ($linecount > 1 && get_config('local_virus_scanner', 'sendmail')) {
            $touser = new \stdClass();
            $touser->email = get_config('local_virus_scanner', 'mailid');
            $touser->id = -99;
            $touser->mailformat = 1;
            $fromuser = \core_user::get_noreply_user();
            $subject = get_string('mailsubject', 'local_virus_scanner', userdate(time(), '%Y-%m-%d'));
            $messagetext = get_string('messagetext', 'local_virus_scanner');
            $messagehtml = get_string('messagehtml', 'local_virus_scanner');
            if (email_to_user($touser, $fromuser, $subject, $messagetext, $messagehtml, $filelocation, $filename)) {
                mtrace("virus scan report mail sent to " . $touser->email . ' successfully');
            }
        } else {
            mtrace('no virus detected');
        }

    }
}
