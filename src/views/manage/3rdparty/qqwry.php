<?php
class QQWry {
    private $fp;
    private $firstip;
    private $lastip;
    private $totalip;
    public function __construct($filename = "../data/qqwry.dat") {
        $this->fp = 0;
        if (($this->fp = fopen(dirname(__FILE__) . '/' . $filename, 'rb')) !== false) {
            $this->firstip = $this->getlong();
            $this->lastip = $this->getlong();
            $this->totalip = ($this->lastip - $this->firstip) / 7;
        }
    }
    private function getlong() {
        //将读取的little-endian编码的4个字节转化为长整型数
        $result = unpack('Vlong', fread($this->fp, 4));
        return $result['long'];
    }
    private function getlong3() {
        $result = unpack('Vlong', fread($this->fp, 3) . chr(0));
        return $result['long'];
    }
    private function packip($ip) {
        return pack('N', intval(ip2long($ip)));
    }
    private function getstring($data = "") {
        $char = fread($this->fp, 1);
        while (ord($char) > 0) {
            $data .= $char;
            $char = fread($this->fp, 1);
        }
        return $data;
    }
    private function getarea() {
        $byte = fread($this->fp, 1);
        switch (ord($byte)) {
            case 0:
                $area = "";
                break;
            case 1:
            case 2:
                fseek($this->fp, $this->getlong3());
                $area = $this->getstring();
                break;
            default:
                $area = $this->getstring($byte);
                break;
        }
        return $area;
    }
    private $provinces = array("黑龙江省", "辽宁省", "吉林省", "河北省", "河南省", "湖北省", "湖南省", "山东省", "山西省", "陕西省","安徽省", "浙江省", "江苏省", "福建省", "广东省", "海南省", "四川省", "云南省", "贵州省", "青海省", "甘肃省","江西省", "台湾省", "内蒙古", "宁夏", "新疆", "西藏", "广西", "北京市", "上海市", "天津市", "重庆市", "香港", "澳门");
    public function getlocation($ip = '') {
        if (!$this->fp)
            return null;
        if (empty($ip))
            $ip = get_client_ip();
        $location['ip'] = gethostbyname($ip);
        $ip = $this->packip($location['ip']);
        $l = 0;
        $u = $this->totalip;
        $findip = $this->lastip;
        while ($l <= $u) {
            $i = floor(($l + $u) / 2);
            fseek($this->fp, $this->firstip + $i * 7);
            $beginip = strrev(fread($this->fp, 4));
            if ($ip < $beginip) {
                $u = $i - 1;
            } else {
                fseek($this->fp, $this->getlong3());
                $endip = strrev(fread($this->fp, 4));
                if ($ip > $endip) {
                    $l = $i + 1;
                } else {
                    $findip = $this->firstip + $i * 7;
                    break;
                }
            }
        }
        fseek($this->fp, $findip);
        $location['beginip'] = long2ip($this->getlong());
        $offset = $this->getlong3();
        fseek($this->fp, $offset);
        $location['endip'] = long2ip($this->getlong());
        $byte = fread($this->fp, 1);
        switch (ord($byte)) {
            case 1:
                $countryOffset = $this->getlong3();
                fseek($this->fp, $countryOffset);
                $byte = fread($this->fp, 1);
                switch (ord($byte)) {
                    case 2:
                        fseek($this->fp, $this->getlong3());
                        $location['country'] = $this->getstring();
                        fseek($this->fp, $countryOffset + 4);
                        $location['area'] = $this->getarea();
                        break;
                    default:
                        $location['country'] = $this->getstring($byte);
                        $location['area'] = $this->getarea();
                        break;
                }
                break;
            case 2:
                fseek($this->fp, $this->getlong3());
                $location['country'] = $this->getstring();
                fseek($this->fp, $offset + 8);
                $location['area'] = $this->getarea();
                break;
            default:
                $location['country'] = $this->getstring($byte);
                $location['area'] = $this->getarea();
                break;
        }
        if (trim($location['country']) == 'CZ88.NET') {
            $location['country'] = '未知';
        }
        if (trim($location['area']) == 'CZ88.NET') {
            $location['area'] = '';
        }
        $location['country'] = @iconv('gbk', 'utf-8', $location['country']);
        $location['area'] = @iconv('gbk', 'utf-8', $location['area']);
        foreach ($this->provinces as $v) {
            if (strpos($location['country'], $v) === 0) {
                $location['province'] = $v;
                $location['city'] = str_replace($v, '', $location['country']);
                break;
            }
        }
        if (empty($location['province']))
            $location['province'] = '';
        if (empty($location['city']))
            $location['city'] = '';
        //sunan moify
        if (in_array($location['province'], $this->provinces)) {
            $location['country'] = "中国";
        }
        return $location;
    }
    public function __destruct() {
        if ($this->fp) {
            fclose($this->fp);
        }
        $this->fp = 0;
    }
}