<?php

namespace Tests\Browser\Support;

use PhpOffice\PhpSpreadsheet\IOFactory;

class ExcelReader
{
    public static function read(string $file): array
    {
        $spreadsheet = IOFactory::load($file);

        return $spreadsheet
            ->getActiveSheet()
            ->toArray(null, true, true, false);
    }

    /*public static function readUsers(string $path): array
    {
        $sheet = IOFactory::load($path)->getActiveSheet();

        $rows = $sheet->toArray(null, true, true, false);

        $header = array_shift($rows);

        $data = [];

        foreach ($rows as $row) {

            if (trim((string)$row[0]) === '') {
                continue;
            }

            $item = array_combine($header, $row);

            $data[] = [
                'name' => $item['name'],
                'ngay_sinh' => $item['ngay_sinh'],
                'username' => $item['username'],
                'password' => $item['password'],
                'role_id' => (string)$item['role_id'],
                'phone' => (string)$item['phone'],
                'email' => $item['email'],
                'chuc_vu_id' => $item['chuc_vu_id'],
                'don_vi_id' => (string)$item['don_vi_id'],

                'la_dai_bieu' => $item['la_dai_bieu'] == 1,
                'tra_loi_chat_van' => $item['tra_loi_chat_van'] == 1,
                'status' => $item['status'] == 1,
            ];
        }

        return $data;
    }

    public static function readKhoaHop(string $path): array
    {
        $sheet = IOFactory::load($path)->getActiveSheet();

        $rows = $sheet->toArray(null, true, true, false);

        $header = array_shift($rows);

        $data = [];

        foreach ($rows as $row) {

            if (trim((string)$row[0]) === '') {
                continue;
            }

            $item = array_combine($header, $row);

            $data[] = [
                'name'  => $item['name'],
                'nam'   => (string)$item['nam'],
                'mo_ta' => $item['mo_ta'] ?? '',
            ];
        }

        return $data;
    }

    public static function readKyHop(string $path): array
    {
        $sheet = IOFactory::load($path)->getActiveSheet();

        $rows = $sheet->toArray(null, true, true, false);

        $header = array_shift($rows);

        $data = [];

        foreach ($rows as $row) {

            if (trim((string)$row[0]) === '') {
                continue;
            }

            $item = array_combine($header, $row);

            $data[] = [
                'name' => $item['name'],
                'khoa_hop_name' => $item['khoa_hop_name'],
                'bat_dau' => $item['bat_dau'],
                'ket_thuc' => $item['ket_thuc'],
                'dia_diem_nhap' => $item['dia_diem_nhap'] ?? '',
                'mo_ta' => $item['mo_ta'] ?? '',
                'type' => (string)$item['type'],
            ];
        }

        return $data;
    }

    public static function readDanhMucFile(string $path): array
    {
        $sheet = IOFactory::load($path)->getActiveSheet();

        $rows = $sheet->toArray(null, true, true, false);
        $header = array_shift($rows);

        $data = [];

        foreach ($rows as $row) {

            if (trim((string)$row[0]) === '') {
                continue;
            }

            $item = array_combine($header, $row);

            $data[] = [
                'name'      => $item['name'],
                'shortName' => $item['shortName'] ?? '',
            ];
        }

        return $data;
    }

    public static function readDonVi(string $path): array
    {
        $sheet = IOFactory::load($path)->getActiveSheet();

        $rows = $sheet->toArray(null, true, true, false);
        $header = array_shift($rows);

        $data = [];

        foreach ($rows as $row) {

            if (trim((string)$row[0]) === '') {
                continue;
            }

            $item = array_combine($header, $row);

            $data[] = [
                'ten_don_vi'     => $item['ten_don_vi'],
                'parent_name'    => $item['parent_name'] ?? '',
                'ten_viet_tat'   => $item['ten_viet_tat'] ?? '',
                'ma_hanh_chinh'  => $item['ma_hanh_chinh'] ?? '',
                'dia_chi'        => $item['dia_chi'] ?? '',
                'dien_thoai'     => $item['dien_thoai'] ?? '',
                'email'          => $item['email'] ?? '',
                'cap_to_chuc'    => (string)$item['cap_to_chuc'],
                'dieu_hanh'      => (string)$item['dieu_hanh'],
            ];
        }

        return $data;
    }*/
}