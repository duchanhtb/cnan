<?php

if (!defined('ALLOW_ACCESS'))
    exit('No direct script access allowed');

/**
 * @author duchanh
 * @copyright 2012
 */
class CFile {
    /**
      -- start Method section  -- */

    /**
     * 	Scope: Public
     * 	Level: Instance
     * 	Constructor
     */
    function CFile() {
        
    }

    /**
     *  
     * 	Scope: Public
     * 	Level: Class
     */
    public static function getFileDir($dir, $ext = false) {
        $result = array();

        if (!is_dir($dir)) {
            return false;
        }
        if ($files_dir = opendir($dir)) {
            while (($file = readdir($files_dir)) !== false) {
                if ($file == '.' || $file == '..')
                    continue;

                if ($ext != false) {
                    if (stristr($file, $ext) !== false) {
                        $result[] = $file;
                    }
                } else {
                    $result[] = $file;
                }
            }
        }

        return $result;
    }

    /**
     * 	Scope: Public
     * 	Level: Class
     */
    public static function uploadFile($temp_file, $file_name, $folder, $aTypeUpload = "") {

        $sOriginalFileName = $file_name;
        $sExtension = substr($file_name, ( strrpos($file_name, '.') + 1));
        $sExtension = strtolower($sExtension);
        $iCounter = 0;
        $sServerDir = $folder;
        while (true) {
            // Compose the file path.
            $sFilePath = $sServerDir . $file_name;

            // If a file with that name already exists.
            if (is_file($sFilePath)) {
                $iCounter++;
                $file_name = CFile::removeExtension($sOriginalFileName) . '_' . $iCounter . '.' . $sExtension;
            } else {
                move_uploaded_file($temp_file, $sFilePath);

                if (is_file($sFilePath)) {
                    $oldumask = umask(0);
                    chmod($sFilePath, 0777);
                    umask($oldumask);
                }
                clearstatcache();
                break;
            }
            clearstatcache();
        }
        return $file_name;
    }

    /**
     * 	Scope: Public
     * 	Level: Class
     */
    public static function removeExtension($fileName) {
        return substr($fileName, 0, strrpos($fileName, '.'));
    }

    /**
     * 	Scope: Public
     */
    public static function unzip() {
        $iParNum = func_num_args();
        $aPar = func_get_args();

        $sPackagePath = $aPar[0]; // absolute path to zip file
        $sDesDir = $aPar[1]; // Path/Path2/Path3/

        CFile::makeDirFromString($sDesDir);
        $vZip = zip_open($sPackagePath);

        if ($vZip) {
            while ($vZipEntry = zip_read($vZip)) {

                $sFileName = zip_entry_name($vZipEntry);

                if (strrpos($sFileName, "/") != ( strlen($sFileName) - 1 )) {
                    $sFilePath = $sDesDir . $sFileName;
                    $sDirPath = substr($sFilePath, 0, strrpos($sFilePath, "/"));
                    CFile::makeDirFromString($sDirPath);
                    zip_entry_open($vZip, $vZipEntry, "r");
                    $sSize = zip_entry_filesize($vZipEntry) + 1;
                    $vBuf = zip_entry_read($vZipEntry, $sSize);
                    $vFp = fopen($sFilePath, "a+");
                    fwrite($vFp, $vBuf);
                    fclose($vFp);
                    zip_entry_close($vZipEntry);
                }
            }
            zip_close($vZip);
        }
    }

    /**
     * 	Scope: Public
     */
    function makeDirFromString() {
        $iParNum = func_num_args();
        $aPar = func_get_args();

        if ($iParNum == 1)
            $sPath = $aPar[0];# Path1/Path2/Path3/

        $aPath = explode("/", $sPath);
        $sNewPath = "";
        foreach ($aPath as $v) {
            $sNewPath .= $v;
            if (!in_array($v, array("", ".", "..")) && !is_dir($sNewPath)) {
                mkdir($sNewPath, 0777);
            }
            $sNewPath .= "/";
        }
    }

    /**
     * 	Scope: Public
     */
    function copyDir() {
        $iParNum = func_num_args();
        $aPar = func_get_args();

        $sSrcDir = $aPar[0]; // Path1/Path2/Path3
        $sDesDir = $aPar[1]; // Path1/Path2/Path3
        if ($iParNum == 3)
            $sFPatern = $aPar[2]; // File name pattern (perl)

        $vDirHandle = opendir($sSrcDir);
        $aFiles = array();
        $aDirs = array();
        $aNotChosenFiles = array(".", "..");
        $aNotChosenDirs = array(".", "..");
        while (false !== ( $sFileName = readdir($vDirHandle) )) {
            if (is_dir($sSrcDir . "/" . $sFileName) && !in_array($sFileName, $aNotChosenDirs)) {
                $aDirs[] = $sFileName;
            } else if (!in_array($sFileName, $aNotChosenFiles)) {
                if ($iParNum == 3) {
                    if (preg_match($sFPattern, $sFileName)) {
                        $aFiles[] = $sFileName;
                    }
                } else {
                    $aFiles[] = $sFileName;
                }
            }
        }

        if (count($aFiles) > 0) {
            foreach ($aFiles as $k => $v) {
                CFile::makeDirFromString($sDesDir);
                copy($sSrcDir . "/" . $v, $sDesDir . "/" . $v);
            }
        }

        foreach ($aDirs as $v) {
            if ($iParNum == 3)
                CFile::copyDir($sSrcDir . "/" . $v, $sDesDir . "/" . $v, $sFPatern);
            else
                CFile::copyDir($sSrcDir . "/" . $v, $sDesDir . "/" . $v);
        }
    }

    /**
     * 	Scope: Public
     */
    function removeDir() {
        $iParNum = func_num_args();
        $aPar = func_get_args();

        $sSrcDir = $aPar[0]; // Path1/Path2/Path3
        if ($iParNum == 2)
            $sFPatern = $aPar[1]; // File name pattern (perl)

        $vDirHandle = opendir($sSrcDir);
        $aFiles = array();
        $aDirs = array();
        $aNotChosenFiles = array(".", "..");
        $aNotChosenDirs = array(".", "..");
        while (false !== ( $sFileName = readdir($vDirHandle) )) {
            if (is_dir($sSrcDir . "/" . $sFileName) && !in_array($sFileName, $aNotChosenDirs)) {
                $aDirs[] = $sFileName;
            } else if (!in_array($sFileName, $aNotChosenFiles)) {
                if ($iParNum == 2) {
                    if (preg_match($sFPattern, $sFileName)) {
                        $aFiles[] = $sFileName;
                    }
                } else {
                    $aFiles[] = $sFileName;
                }
            }
        }

        if (count($aFiles) > 0) {
            foreach ($aFiles as $k => $v) {
                unlink($sSrcDir . "/" . $v);
            }
        }

        foreach ($aDirs as $v) {
            if ($iParNum == 2)
                CFile::removeDir($sSrcDir . "/" . $v, $sFPatern);
            else
                CFile::removeDir($sSrcDir . "/" . $v);
        }

        closedir($vDirHandle);
        rmdir($sSrcDir);
    }

}