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
 * Extend navigation to add new options.
 *
 * @package    local_th_dashboard
 * @author     Carlos Escobedo <http://www.twitter.com/carlosagile>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @copyright  2017 Carlos Escobedo <http://www.twitter.com/carlosagile>)
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Extend Navigation block and add options
 *
 * @param global_navigation $navigation {@link global_navigation}
 * @return void
 */
function local_th_dashboard_extend_navigation(global_navigation $navigation) {
    $systemcontext = context_system::instance();
    $settings = get_config('local_th_dashboard');
    if (has_capability('local/th_dashboard:viewthdashboard', $systemcontext)) {
        $th_url = new moodle_url('/local/th_dashboard/view.php', array('key' => 'thdashboard'));
        $main_node = $navigation->add(get_string('THname', 'local_th_dashboard'),$th_url, navigation_node::TYPE_CONTAINER, null, 'thdashboard');
        $main_node->nodetype = 1;
        // $main_node->forceopen = true;
        // $main_node->showinflatnavigation = true;

        // $sub_node = $main_node->add(get_string('pluinname', 'local_th_dashboard'),'/local/th_dashboard/');
        if (!empty($settings->menuitems) && $settings->enabled) {
            $menu = new custom_menu($settings->menuitems, current_language());
            $count = 0;
            if ($menu->has_children()) {
                foreach ($menu->get_children() as $item) {
                    navigation_custom_menu_item($item, 1, $main_node, $settings->flatenabled, $count);
                }
            }
        }
    }

}

/**
 * ADD custom menu in navigation recursive childs node
 * Is like render custom menu items
 *
 * @param custom_menu_item $menunode {@link custom_menu_item}
 * @param int $parent is have a parent and it's parent itself
 * @param object $pmasternode parent node
 * @param int $flatenabled show master node in boost navigation
 * @return void
 */
function navigation_custom_menu_item(custom_menu_item $menunode, $parent, $pmasternode, $flatenabled, &$count) {
    global $PAGE, $CFG;

    static $submenucount = 0;

    if ($menunode->has_children()) { // node da cap
        $thkey = get_string('thkey', 'local_th_dashboard') . '_' .$count;
        $submenucount++;
        $url = $CFG->wwwroot;
        if ($menunode->get_url() !== null) {
            $url = new moodle_url($menunode->get_url());
        } else {
            $url = new moodle_url('/local/th_dashboard/view.php', array('key' => $thkey));
        }
        if ($parent > 0) { 
            $masternode = $pmasternode->add(local_th_dashboard_get_string($menunode->get_text()),
                                            $url, navigation_node::TYPE_CONTAINER, null, $thkey);
            $masternode->title($menunode->get_title());
        } else {
            $masternode = $PAGE->navigation->add(local_th_dashboard_get_string($menunode->get_text()),
                                            $url, navigation_node::TYPE_CONTAINER, null, $thkey);
            $masternode->title($menunode->get_title());
            if ($flatenabled) {
                $masternode->isexpandable = true;
                $masternode->showinflatnavigation = true;
            }
        }
        $count++;
        foreach ($menunode->get_children() as $menunode) {
            navigation_custom_menu_item($menunode, $submenucount, $masternode, $flatenabled, $count);
        }
    } else {
        $thkey = get_string('thkey', 'local_th_dashboard') . '_' .$count;
        $url = $CFG->wwwroot;
        if ($menunode->get_url() !== null) {
            $url = new moodle_url($menunode->get_url());
        } else {
            $url = new moodle_url('/local/th_dashboard/view.php', array('key' => $thkey));
        }
        if ($parent) {
            $childnode = $pmasternode->add(local_th_dashboard_get_string($menunode->get_text()),
                                        $url, navigation_node::TYPE_CUSTOM, null, $thkey);
            $childnode->title($menunode->get_title());
        } else {
            $masternode = $PAGE->navigation->add(local_th_dashboard_get_string($menunode->get_text()),
                                        $url, navigation_node::TYPE_CONTAINER, null, $thkey);
            $masternode->title($menunode->get_title());
            if ($flatenabled) {
                $masternode->isexpandable = true;
                $masternode->showinflatnavigation = true;
            }
        }
        $count++;
    }

    return true;
}

/**
 * Translate Custom Navigation Nodes
 *
 * This function is based in a short peace of Moodle code
 * in  Name processing on user_convert_text_to_menu_items.
 *
 * @param string $string text to translate.
 * @return string
 */
function local_th_dashboard_get_string($string) {
    $title = $string;
    $text = explode(',', $string, 2);
    if (count($text) == 2) {
        // Check the validity of the identifier part of the string.
        if (clean_param($text[0], PARAM_STRINGID) !== '') {
            // Treat this as atext language string.
            $title = get_string($text[0], $text[1]);
        }
    }
    return $title;
}
