<?php
    declare(strict_types=1);

    namespace sbf\components;

    error_reporting(E_ALL);
    ini_set('display_errors', '1');

    use sbf\components\Component;

    trait ComponentObjectArrayTraits {
        private function GetObjectArrayElementCount(array &$arr, string $objectType = "") : int {
            return count($arr);
        }

        private function GetObjectArrayElementKeys(array &$arr, string $objectType = "") : array {   
            return array_keys($this->GetObjectArrayElements($arr, $objectType));   
        }

        private function GetObjectArrayElements(array &$arr, string $objectType = "") : array {
            $returnValue = $arr;

            if (($objectType = trim($objectType)) != "" ) {
                $returnValue = array_filter($returnValue, function ($v, $k) use ($objectType) {
                    return is_a($v, $objectType);
                }, ARRAY_FILTER_USE_BOTH);
            }

            return $returnValue;
        }

        private function GetObjectArrayElement(array &$arr, string $elementKey) : ?Component {
            if (array_key_exists($elementKey, $arr))
                return $arr[$elementKey];

            $this->AddError(E_USER_ERROR, "No element exists with the key of '$elementKey'");

            return null;
        }

        private function ObjectArrayElementExists(array &$arr, Component $element) : bool {
            return in_array($element, $arr, true);
        }

        private function ObjectArrayAddElement(array &$arr, Component $element) : bool {
            if (!in_array($element, $arr, true)) {
                if (!array_key_exists($element->GetName(), $arr)) {
                    $arr[$element->GetName()] = $element;
            
                    if (!is_null($componentsParent = $element->GetParent()))
                        $componentsParent->RemoveComponent($element);

                    $element->parent = $this;

                    return true;
                } else {
                    $this->AddError(E_USER_ERROR, "A element already exists with the name '" . $element->GetName() . "'");
                }
            } else {
                $this->AddError(E_USER_ERROR, "The element already exists");            
            }

            return false;
        }

        private function ObjectArrayRemoveElement(array &$arr, Component $element) : bool {
            if (!array_key_exists($element->GetName(), $arr)) {            
                $this->AddError(E_USER_ERROR, "The element does not exist");
                
                return false;
            }            

            unset($arr[$element->GetName()]);

            $element->parent = null;

            return true;
        }
    }
?>