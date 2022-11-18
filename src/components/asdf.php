        protected function GetExtensionsCount(string $extensionType = "") : int {
            $this->ProcessHook("GetExtensionsCount_FIHOOK", [$this, &$extensionType]);

            $returnValue = $this->GetObjectArrayElementCount($this->extensions, $extensionType);

            return $this->ProcessHook("GetExtensionsCount_FRHOOK", [$this, $returnValue, $extensionType]);
        }

        protected function GetExtensions(string $extensionType = "") : array {
            $this->ProcessHook("GetExtensions_FIHOOK", [$this, &$extensionType]);

            $returnValue = $this->GetObjectArrayElements($this->extensions, $extensionType);

            return $this->ProcessHook("GetExtensions_FRHOOK", [$this, $returnValue, $extensionType]);
        }

        protected function GetExtensionNames(string $extensionType = "") : array {
            $this->ProcessHook("GetExtensionNames_FIHOOK", [$this]);

            $returnValue = $this->GetObjectArrayElementKeys($this->extensions, $extensionType);            
            
            return $this->ProcessHook("GetExtensionNames_FRHOOK", [$this, $returnValue]);
        }

        protected function GetExtension(string $name) : ?Extension {
            $this->ProcessHook("GetExtension_FIHOOK", [$this, &$name]);

            $returnValue = $this->GetObjectArrayElement($this->extensions, $name);            

            return $this->ProcessHook("GetExtension_FRHOOK", [$this, $returnValue, $name]);
        }

        protected function ExtensionExists(Extension $extension) : bool {
            $this->ProcessHook("ExtensionExists_FIHOOK", [$this, &$extension]);

            $returnValue = $this->ObjectArrayElementExists($this->extensions, $extension);
            
            return $this->ProcessHook("ExtensionExists_FRHOOK", [$this, $returnValue, $extension]);
        }

        protected function AddExtension(Extension $extension) : bool {
            usleep(1000);
            $this->ProcessHook("AddExtension_FIHOOK", [$this, &$extension]);

            $returnValue = $this->ObjectArrayAddElement($this->extensions, $extension);            

            if ($extension instanceof Extension)
                $returnValue = $extension->InitExtension();

            return $this->ProcessHook("AddExtension_FRHOOK", [$this, $returnValue, $extension]);
        }

        protected function RemoveExtension(Extension $extension) : bool {
            usleep(1000);
            $this->ProcessHook("RemoveExtension_FIHOOK", [$this, &$extension]);
            
            $returnValue = $this->ObjectArrayRemoveElement($this->extensions, $extension);

            return $this->ProcessHook("RemoveExtension_FRHOOK", [$this, $returnValue, $extension]);
        }