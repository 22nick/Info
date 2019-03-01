<?php
namespace Tunik\Info\Setup;
/**
 * The MIT License (MIT)
 * Copyright (c) 2015 - 2019 Pulse Storm LLC, Alan Storm
 * 
 * Permission is hereby granted, free of charge, to any person obtaining 
 * a copy of this software and associated documentation files (the 
 * "Software"), to deal in the Software without restriction, including 
 * without limitation the rights to use, copy, modify, merge, publish, 
 * distribute, sublicense, and/or sell copies of the Software, and to 
 * permit persons to whom the Software is furnished to do so, subject to 
 * the following conditions:
 * 
 * The above copyright notice and this permission notice shall be included 
 * in all copies or substantial portions of the Software.
 * 
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS 
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF 
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. 
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY 
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT 
 * OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR 
 * THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */

class Scripts
{
    protected $dirReader;
    protected $currentModuleVersionFromDisk=false;
    public function __construct(
        \Magento\Framework\Module\Dir\Reader $dirReader
    )
    {
        $this->dirReader = $dirReader;
    }

    public function run($setup, $context, $type)
    {
        foreach($this->getSetupScripts($type) as $version=>$script)
        {
            $this->runUpgradeScriptIfNeeded($version, $script, $context, $setup);
        }            
    }
    
    protected function runUpgradeScriptIfNeeded($version, $script, $context, $setup)
    {        
        if(!version_compare($context->getVersion(), $version, '<'))
        {
            return;
        }

        if(version_compare($this->getCurrentModuleVersionFromDisk(), $version) === -1)
        {
            return;
        }
        include $script;                
    }  
        
    protected function getSetupScripts($type)
    {
        $files = glob($this->getBaseModuleDirectory() . '/upgrade_scripts/' .
            $type . '/*.*.*.php');

        usort($files, function($a, $b){
            $a = pathinfo($a)['filename'];
            $b = pathinfo($b)['filename'];
            return version_compare($a, $b);
        });
                    
        $withVersionKeys = [];
        foreach($files as $file)
        {
            $withVersionKeys[pathinfo($file)['filename']] = $file;
        }
        
        return $withVersionKeys;
    }
    
    protected function getModuleNameFromStaticClassName()
    {
        $parts = explode("\\", static::class);
        return $parts[0] . '_' . $parts[1];
    }
    
    protected function getBaseModuleDirectory()
    {
        return $this->dirReader->getModuleDir('',$this->getModuleNameFromStaticClassName());        
    }

    /**
     * We don't trust any of the standard class mechanisms to stay stable version
     * to version, and that seems important in an upgrade class that shouldn't
     * ever change.
     */    
    protected function getCurrentModuleVersionFromDisk()
    {
        if(!$this->currentModuleVersionFromDisk)
        {
            $xml = $this->loadXmlFile($this->getBaseModuleDirectory() . '/etc/module.xml');
            $this->currentModuleVersionFromDisk = $xml->module['setup_version'];
        }
        return $this->currentModuleVersionFromDisk;
    }
    
    protected function loadXmlFile($path)
    {
        return simplexml_load_file($path);
    }      
}
