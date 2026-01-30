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
 * Adaptable theme.
 *
 * @package    theme_adaptable
 * @copyright  2026 G J Barnard
 *               {@link https://moodle.org/user/profile.php?id=442195}
 *               {@link https://gjbarnard.co.uk}
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

namespace theme_adaptable;

use stdClass;

/**
 * Settings toolbox.
 */
class upgrade_toolbox {
    /**
     * Process updates to settings based upon feature version.
     * Note: Does not cope with file props!
     *
     * @param array $props Reference to the properties from the properties import.
     * @param string $pluginfrankenstyle Frankenstyle name of the plugin.
     * @param int $propsfeatureversion Feature version before upgrade / value in properties.
     *
     * @return array Of changes as localised strings.
     */
    public static function process_settings_name_updates(&$props, $pluginfrankenstyle, $propsfeatureversion) {
        $upgrading = (empty($props));
        $changes = [];
        $changed = [];

        // From and to = change, only from = remove and 'to' only will use setting default value.
        if ($propsfeatureversion < 2025080200) {
            // Changes in 2025080200.
            $change = new stdClass();
            $change->from = 'topmenufontsize';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'coursepageheaderhidesitetitle';
            $change->to = 'coursepageheaderhidetitle';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'enableheading';
            $change->to = 'enablecoursetitle';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'enablezoomshowtext';
            $change->to = 'navbardisplaytitles';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'responsivecoursetitle';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'responsivesitetitle';
            $change->to = 'responsiveheadertitle';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'jssection';
            $change->to = 'customjs';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'jssectionrestricted';
            $change->to = 'customjsrestricted';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'pageheaderheight';
            $change->to = 'headermainrowminheight';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'dividingline';
            $change->to = 'headertoprowdividingline';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'dividingline2';
            $change->to = 'footerdividingline';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'headerbkcolor';
            $change->to = 'headertoprowbkcolour';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'headertextcolor';
            $change->to = 'headertoprowtextcolour';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'headerbkcolor2';
            $change->to = 'headermainrowbkcolour';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'headertextcolor2';
            $change->to = 'headermainrowtextcolour';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'responsiveheader';
            $change->to = 'responsiveheader';
            $change->convert = function ($value) {
                return str_replace('block', 'flex', $value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'maincolor';
            $change->to = 'maincolour';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'fontcolor';
            $change->to = 'fontcolour';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'regionmaincolor';
            $change->to = 'regionmaincolour';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'regionmaintextcolor';
            $change->to = 'regionmaintextcolour';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'linkcolor';
            $change->to = 'linkcolour';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'dimmedtextcolor';
            $change->to = 'dimmedtextcolour';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'coursepageblocksliderenabled';
            $change->to = 'coursepageblockinfoenabled';
            $changes[] = $change;
        }

        if ($propsfeatureversion < 2025112100) {
            // Changes in 2025112100.
            $change = new stdClass();
            $change->from = 'coursepageblocklayoutlayoutbottomrow2';
            $change->to = 'coursepageblocklayoutbottomrow1';
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'coursepageblocklayoutlayouttoprow1';
            $change->to = 'coursepageblocklayouttoprow1';
            $changes[] = $change;
        }

        if ($propsfeatureversion < 2026010800) {
            // Changes in 2026010800.
            $change = new stdClass();
            $change->from = 'blockheaderborderbottom';
            $change->to = 'blockheaderborderbottom';
            $change->convert = function ($value) {
                return intval($value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'blockheaderborderleft';
            $change->to = 'blockheaderborderleft';
            $change->convert = function ($value) {
                return intval($value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'blockheaderborderright';
            $change->to = 'blockheaderborderright';
            $change->convert = function ($value) {
                return intval($value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'blockheaderbordertop';
            $change->to = 'blockheaderbordertop';
            $change->convert = function ($value) {
                return intval($value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'blockheaderbottomradius';
            $change->to = 'blockheaderbottomradius';
            $change->convert = function ($value) {
                return intval($value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'blockheadertopradius';
            $change->to = 'blockheadertopradius';
            $change->convert = function ($value) {
                return intval($value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'blockiconsheadersize';
            $change->to = 'blockiconsheadersize';
            $change->convert = function ($value) {
                return intval($value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'blockmainborderbottom';
            $change->to = 'blockmainborderbottom';
            $change->convert = function ($value) {
                return intval($value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'blockmainborderleft';
            $change->to = 'blockmainborderleft';
            $change->convert = function ($value) {
                return intval($value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'blockmainborderright';
            $change->to = 'blockmainborderright';
            $change->convert = function ($value) {
                return intval($value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'blockmainbordertop';
            $change->to = 'blockmainbordertop';
            $change->convert = function ($value) {
                return intval($value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'blockmainbottomradius';
            $change->to = 'blockmainbottomradius';
            $change->convert = function ($value) {
                return intval($value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'blockmaintopradius';
            $change->to = 'blockmaintopradius';
            $change->convert = function ($value) {
                return intval($value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'buttonloginheight';
            $change->to = 'buttonloginheight';
            $change->convert = function ($value) {
                return intval($value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'buttonloginpadding';
            $change->to = 'buttonloginpadding';
            $change->convert = function ($value) {
                return intval($value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'buttonloginmargintop';
            $change->to = 'buttonloginmargintop';
            $change->convert = function ($value) {
                return intval($value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'buttonradius';
            $change->to = 'buttonradius';
            $change->convert = function ($value) {
                return intval($value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'coursesectionactivityborderwidth';
            $change->to = 'coursesectionactivitybottomborderwidth';
            $change->convert = function ($value) {
                return intval($value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'coursesectionactivityiconsize';
            $change->to = 'coursesectionactivityiconsize';
            $change->convert = function ($value) {
                return intval($value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'coursesectionactivityleftborderwidth';
            $change->to = 'coursesectionactivityleftborderwidth';
            $change->convert = function ($value) {
                return intval($value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'coursesectionactivitymarginbottom';
            $change->to = 'coursesectionactivitymarginbottom';
            $change->convert = function ($value) {
                return intval($value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'coursesectionactivitymargintop';
            $change->to = 'coursesectionactivitymargintop';
            $change->convert = function ($value) {
                return intval($value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'coursesectionborderradius';
            $change->to = 'coursesectionborderradius';
            $change->convert = function ($value) {
                return intval($value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'coursesectionheaderborderradiusbottom';
            $change->to = 'coursesectionheaderborderradiusbottom';
            $change->convert = function ($value) {
                return intval($value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'coursesectionheaderborderradiustop';
            $change->to = 'coursesectionheaderborderradiustop';
            $change->convert = function ($value) {
                return intval($value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'coursesectionborderwidth';
            $change->to = 'coursesectionborderwidth';
            $change->convert = function ($value) {
                return intval($value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'coursesectionheaderborderwidth';
            $change->to = 'coursesectionheaderborderwidth';
            $change->convert = function ($value) {
                return intval($value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'edithorizontalpadding';
            $change->to = 'edithorizontalpadding';
            $change->convert = function ($value) {
                return intval($value);
            };

            $changes[] = $change;
            $change = new stdClass();
            $change->from = 'emoticonsize';
            $change->to = 'emoticonsize';
            $change->convert = function ($value) {
                return intval($value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'fontblockheadersize';
            $change->to = 'fontblockheadersize';
            $change->convert = function ($value) {
                return intval($value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'fonttitlesize';
            $change->to = 'fonttitlesize';
            $change->convert = function ($value) {
                return intval($value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'menufontpadding';
            $change->to = 'menufontpadding';
            $change->convert = function ($value) {
                return intval($value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'menufontsize';
            $change->to = 'menufontsize';
            $change->convert = function ($value) {
                return intval($value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'navbardropdownborderradius';
            $change->to = 'navbardropdownborderradius';
            $change->convert = function ($value) {
                return intval($value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'responsivesocialsize';
            $change->to = 'responsivesocialsize';
            $change->convert = function ($value) {
                return intval($value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'slidermarginbottom';
            $change->to = 'slidermarginbottom';
            $change->convert = function ($value) {
                return intval($value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'slidermargintop';
            $change->to = 'slidermargintop';
            $change->convert = function ($value) {
                return intval($value);
            };
            $changes[] = $change;

            $change = new stdClass();
            $change->from = 'socialsize';
            $change->to = 'socialsize';
            $change->convert = function ($value) {
                return intval($value);
            };
            $changes[] = $change;
        }

        if ((!empty($changes)) && ($upgrading)) {
            $props = toolbox::compile_properties($pluginfrankenstyle)[toolbox::PROPS];
        }

        foreach ($changes as $change) {
            if (array_key_exists($change->from, $props)) {
                // Make the change.
                if (!empty($change->to)) {
                    // Replacement.
                    if (!array_key_exists($change->to, $props)) {
                        // Replacement not defined, make new to be the old value.
                        if (empty($change->convert)) {
                            $tovalue = $props[$change->from];
                        } else {
                            // Convert too.
                            $tovalue = ($change->convert)($props[$change->from]);
                        }
                        if ($upgrading) {
                            set_config($change->to, $tovalue, $pluginfrankenstyle);
                        } else {
                            // Set in properties as version will have this new setting and in effect
                            // the property file is updating it, possibly.
                            $props[$change->to] = $tovalue;
                        }
                        $changed[] = get_string(
                            'settingschangechanged',
                            $pluginfrankenstyle,
                            [
                                'from' => $change->from,
                                'to' => $change->to,
                                'value' => $tovalue,
                            ]
                        );
                    } else if (($change->from == $change->to) && (!empty($change->convert))) {
                        // Existing variable conversion.
                        $fromvalue = $props[$change->from];
                        $tovalue = ($change->convert)($fromvalue);
                        if ($fromvalue == $tovalue) {
                            // No actual change!
                            continue;
                        }
                        if ($upgrading) {
                            set_config($change->to, $tovalue, $pluginfrankenstyle);
                        } else {
                            // Set in properties as version will have this new setting and in effect
                            // the property file is updating it, possibly.
                            $props[$change->to] = $tovalue;
                        }
                        $changed[] = get_string(
                            'settingschangevalue',
                            $pluginfrankenstyle,
                            [
                                'from' => $change->from,
                                'valuefrom' => $fromvalue,
                                'valueto' => $tovalue,
                            ]
                        );
                    } else {
                        // Else replacement already defined, just remove old setting as new has superceded it.
                        $changed[] = get_string(
                            'settingschangealreadydefined',
                            $pluginfrankenstyle,
                            [
                                'from' => $change->from,
                                'to' => $change->to,
                                'fromvalue' => $props[$change->from],
                                'tovalue' => $props[$change->to],
                            ]
                        );
                    }
                    if (!$upgrading) {
                        if (!((!empty($change->to)) && ($change->from == $change->to))) {
                            // Remove from properties so not shown as ignored, rather that it is reported in
                            // 'changed / alreadydefined'.
                            unset($props[$change->from]);
                        } // Else is a change of value;
                    }
                } else if ($upgrading) { // Else deletion.
                    $changed[] = get_string(
                        'settingschangedeleted',
                        $pluginfrankenstyle,
                        [
                            'from' => $change->from,
                            'value' => $props[$change->from],
                        ]
                    );
                }
                if ($upgrading) {
                    if (!((!empty($change->to)) && ($change->from == $change->to))) {
                        // Remove from, being the old.
                        unset_config($change->from, $pluginfrankenstyle);
                    } // Else is a change of value;
                } // Else as no longer exists, then will be reported as 'Ignored'.
            } // Else change does not appear in the properties file when importing or the database when upgrading.
        }

        return $changed;
    }

    /**
     * Process updates to settings area based upon feature version.
     * Note: Only use with confightmleditor settings.
     *
     * @param array $props Reference to the properties from the properties import.
     * @param string $pluginfrankenstyle Frankenstyle name of the plugin.
     * @param int $propsfeatureversion Feature version before upgrade / value in properties.
     *
     * @return array Of changes as localised strings.
     */
    public static function process_settings_area_updates(&$props, $pluginfrankenstyle, $propsfeatureversion) {
        $upgrading = (empty($props));
        $changes = [];
        $changed = [];

        // From and to = change, only from = remove and 'to' only will use setting default value.
        if ($propsfeatureversion < 2025112200) {
            // Changes in 2025112200.

            if ($upgrading) {
                $props = toolbox::compile_properties($pluginfrankenstyle)[toolbox::PROPS];
            }

            // Alert count.
            if (!empty($props['alertcount'])) {
                for ($alertindex = 1; $alertindex <= $props['alertcount']; $alertindex++) {
                    $change = new stdClass();
                    $change->name = 'alerttext' . $alertindex;
                    $change->from = 'adaptablemarkettingimages';
                    $change->to = 'shed_alerttext';
                    $change->toitemid = $alertindex;
                    $changes[] = $change;
                }
            }

            // Footer blocks.  Ref: get_footer_blocks().
            $helper = toolbox::admin_settings_layout_helper('footerlayoutrow', 3, $props);
            if ($helper['totalblocks'] > 0) {
                $blockcount = 0;
                foreach ($helper['rows'] as $row) {
                    foreach ($row as $block) {
                        $blockcount++;
                        $footercontent = 'footer' . $blockcount . 'content';
                        if (!empty($props[$footercontent])) {
                            $change = new stdClass();
                            $change->name = $footercontent;
                            $change->from = 'adaptablemarkettingimages';
                            $change->to = 'shed_footercontent';
                            $change->toitemid = $blockcount;
                            $changes[] = $change;
                        }
                    }
                }
            }

            // Footnote.
            if (!empty($props['footnote'])) {
                $change = new stdClass();
                $change->name = 'footnote';
                $change->from = 'adaptablemarkettingimages';
                $change->to = 'shed_footnote';
                $changes[] = $change;
            }

            // Infobox.
            if (!empty($props['infobox'])) {
                $change = new stdClass();
                $change->name = 'infobox';
                $change->from = 'adaptablemarkettingimages';
                $change->to = 'shed_infobox';
                $change->toitemid = 1;
                $changes[] = $change;
            }

            // Infobox two.
            if (!empty($props['infobox2'])) {
                $change = new stdClass();
                $change->name = 'infobox2';
                $change->from = 'adaptablemarkettingimages';
                $change->to = 'shed_infobox';
                $change->toitemid = 2;
                $changes[] = $change;
            }

            // Login text box top.
            if (!empty($props['logintextboxtop'])) {
                $change = new stdClass();
                $change->name = 'logintextboxtop';
                $change->from = 'adaptablemarkettingimages';
                $change->to = 'shed_logintextboxtop';
                $changes[] = $change;
            }

            // Login text box bottom.
            if (!empty($props['logintextboxbottom'])) {
                $change = new stdClass();
                $change->name = 'logintextboxbottom';
                $change->from = 'adaptablemarkettingimages';
                $change->to = 'shed_logintextboxbottom';
                $changes[] = $change;
            }

            // Marketing blocks.  Ref: get_marketing_blocks().
            $helper = toolbox::admin_settings_layout_helper('marketlayoutrow', 5, $props);
            if ($helper['totalblocks'] > 0) {
                $blockcount = 0;
                foreach ($helper['rows'] as $row) {
                    foreach ($row as $block) {
                        $blockcount++;
                        $fieldname = 'market' . $blockcount;
                        if (!empty($props[$fieldname])) {
                            $change = new stdClass();
                            $change->name = $fieldname;
                            $change->from = 'adaptablemarkettingimages';
                            $change->to = 'shed_market';
                            $change->toitemid = $blockcount;
                            $changes[] = $change;
                        }
                    }
                }
            }

            // News ticker count.
            if (!empty($props['newstickercount'])) {
                for ($newstickerindex = 1; $newstickerindex <= $props['newstickercount']; $newstickerindex++) {
                    $change = new stdClass();
                    $change->name = 'tickertext' . $newstickerindex;
                    $change->from = 'adaptablemarkettingimages';
                    $change->to = 'shed_tickertext';
                    $change->toitemid = $newstickerindex;
                    $changes[] = $change;
                }
            }

            // Slider.
            if (!empty($props['slidercount'])) {
                for ($noslides = 1; $noslides <= $props['slidercount']; $noslides++) {
                    $change = new stdClass();
                    $change->name = 'p' . $noslides . 'cap';
                    $change->from = 'adaptablemarkettingimages';
                    $change->to = 'shed_pcap';
                    $change->toitemid = $noslides;
                    $changes[] = $change;
                }
            }
        }

        if (!empty($changes)) {
            foreach ($changes as $change) {
                $changevalue = $props[$change->name];

                $toitemid = (!empty($change->toitemid)) ? $change->toitemid : 0;
                $changedvalue = \theme_adaptable\admin_setting_confightmleditor::area_move(
                    $changevalue,
                    $change->from,
                    $change->to,
                    $toitemid,
                    $pluginfrankenstyle
                );

                if ($upgrading) {
                    // Change the database value.
                    set_config($change->name, $changedvalue, $pluginfrankenstyle);
                } else {
                    // Change the supplied properties value so that will be then updated by the caller
                    // in the process of importing the properties.
                    $props[$change->name] = $changedvalue;
                }

                if ($changevalue != $changedvalue) {
                    $changed[] = get_string(
                        'settingschangevalue',
                        $pluginfrankenstyle,
                        [
                            'from' => $change->name,
                            'valuefrom' => htmlspecialchars($changevalue),
                            'valueto' => htmlspecialchars($changedvalue),
                        ]
                    );
                }
            }
        }

        return $changed;
    }
}
