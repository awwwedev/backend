<?php


namespace Tests\common;


trait FileSupporting
{
    public function storageHaveFileInStore($filePath, $diskInst)
    {
        return $diskInst->exists(str_replace('/storage/', '', $filePath));
    }
}
