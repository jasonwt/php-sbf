<?php
    declare(strict_types=1);

    namespace sbf\traits\components;

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

        private function ObjectArrayReplaceElementByName(array &$arr, string $name, Component $newElement) : ?Component {
            if (array_key_exists($name, $arr))
                return $this->ObjectArrayReplaceElement($arr, $arr[$name], $newElement);
            
            return null;
        }

        private function ObjectArrayReplaceElement(array &$arr, Component $element, Component $newElement) : ?Component {
            $returnValue = null;
            
            if ($element != $newElement) {
                if (in_array($element, $arr, true)) {
                    if (!in_array($newElement, $arr, true)) {
                        $newArray = [];                    

                        foreach ($arr as $elementName => $elementValue) {
                            if ($elementName == $element->name)
                                $newArray[$newElement->name] = $newElement;
                            else
                                $newArray[$elementName] = $elementValue;
                        }

                        if ($this->RemoveComponent($element)) {
                            $arr = $newArray;

                            $returnValue = $newElement;
                        } else {
                            $this->AddError(E_USER_ERROR, "this->RemoveComponent() failed.");
                        }                        
                    } else {
                        $this->AddError(E_USER_ERROR, "newElement already exists.");
                    }                
                } else {
                    $this->AddError(E_USER_ERROR, "element does not exist.");
                }
            } else {
                $this->AddError(E_USER_WARNING, "element and newElement are the same object");
            }

            return $returnValue;
        }

        private function ObjectArrayAddElement(array &$arr, Component $element, ?string $beforeElement = null) : ?Component {
            if (!in_array($element, $arr, true)) {
                if (!array_key_exists($element->GetName(), $arr)) {
                    if (!is_null($componentsParent = $element->GetParent()))
                        $componentsParent->RemoveComponent($element);

                    $newArray = [];

                    foreach ($arr as $k => $v) {
                        if ($beforeElement == $k)
                            $newArray[$element->GetName()]  = $element;

                        $newArray[$k] = $v;
                    }

                    if (count($newArray) == count($arr)) {
                        $newArray[$element->GetName()] = $element;

                        if (!is_null($beforeElement))
                            $this->AddError(E_USER_WARNING, "beforeElement '$beforeElement' not found.");
                    }

                    $arr = $newArray;

//                    $arr[$element->GetName()] = $element;
            
                    

                    $element->parent = $this;

                    return $element;
                } else {
                    $this->AddError(E_USER_ERROR, "A element already exists with the name '" . $element->GetName() . "'");
                }
            } else {
                $this->AddError(E_USER_ERROR, "The element already exists");            
            }

            return null;
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