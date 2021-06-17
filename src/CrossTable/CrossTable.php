<?php

declare(strict_types=1);

namespace Table\CrossTable;


class CrossTable
{
    /**
     * $items = [
     *     ViewID_0 => [ 'GROUP_ID' => 'cross id', attributeID_0 => 'attributeValue_0', ... , attributeID_N => 'attributeValue_N' ],
     *     ViewID_1 => [ 'GROUP_ID' => 'cross id', attributeID_0 => 'attributeValue_0', ... , attributeID_N => 'attributeValue_N' ],
     *     ...
     *     viewID_N => [ 'GROUP_ID' => 'cross id', attributeID_0 => 'attributeValue_0', ... , attributeID_N => 'attributeValue_N' ],
     * ];
     *
     * $ignorableKeys = [
     *     attributeID_0,
     *     ...
     *     attributeID_N
     * ];
     *
     * @param array $items for required structure see example above
     * @param array $ignorableKeys
     * @return array
     */
    public static function addOrCreate(array $items, array $ignorableKeys): array
    {
        $result = [];

        // create groups
        foreach ($items as $itemID => $item) {
            $group = [];
            $group['ITEM_ID'] = $itemID;
            $groupID = $item['GROUP_ID'];

            foreach ($item as $attributeID => $value) {
                if (!in_array($attributeID, $ignorableKeys)) {
                    $group[$attributeID] = $value;
                }
            }

            // first group
            if (sizeof($result) === 0) {
                $result[0][$groupID] = $group;
            }
            // groups 2...n
            // = find group with identical values or add new one
            else {
                $hit = false;
                foreach ($result as $rowIndex => $rowItems) {
                    foreach ($rowItems as $rowItemGroupID => $rowItemValues) {
                        if ($rowItemGroupID === $groupID) {
                            continue; // skip identical group ids
                        }

                        $riv = $rowItemValues; // items already in group
                        $gv = $group; // new item

                        // remove safe deviations, aka "ignoreable keys" (e.g. group key)
                        foreach ($ignorableKeys as $ignoreableKey) {
                            if (array_key_exists($ignoreableKey, $riv)) {
                                unset($riv[$ignoreableKey]);
                            }
                            if (array_key_exists($ignoreableKey, $gv)) {
                                unset($gv[$ignoreableKey]);
                            }
                            if (array_key_exists('ITEM_ID', $riv)) {
                                unset($riv['ITEM_ID']);
                            }
                            if (array_key_exists('ITEM_ID', $gv)) {
                                unset($gv['ITEM_ID']);
                            }
                        }

                        $intersection = array_intersect($riv, $gv);

                        if (sizeof($intersection) === (sizeof($gv))) {
                            if (array_key_exists($groupID, $rowItems)) {
                                continue;
                            }
                            $result[$rowIndex][$groupID] = $group;
                            $hit = true;
                        }
                    }
                }
                // no existing group matches, add new one
                if (!$hit) {
                    $result[][$groupID] = $group;
                }
            }
        }

        return $result;
    }

    private function __construct() { }
}