<?php
require_once __DIR__ . '/../models/TrangChu.php';
require_once __DIR__ . '/../models/GioHang.php';
require_once __DIR__ . '/../models/Ban.php';
require_once __DIR__ . '/../models/ThongTinDatBan.php';
require_once __DIR__ . '/../models/LoaiMon.php';
class TrangChuController
{
    function MonAn()
    {
        session_start();
        $monmodel = new TrangChu();
        $monlist = $monmodel->getAll();
        $loaimonModel = new loaimon();
        $loaimonList = $loaimonModel->getAll();
        if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {

            header("Location: views/index.php");
            exit();
        } else {
            require_once __DIR__ . '../../views/MonAn.php';
        }
    }
    function MenuLoaiMon($maloai)
    {
        session_start();
        $monmodel = new TrangChu();
        $monlist = $monmodel->MenuLoaiMon($maloai);
        $loaimonModel = new loaimon();
        $loaimonList = $loaimonModel->getAll();
        if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {

            header("Location: views/index.php");
            exit();
        } else {
            require_once __DIR__ . '../../views/MenuLoaiMon.php';
        }
    }

    public function index()
    {
        session_start();
        $_SESSION['username'] = null;
        $monmodel = new TrangChu();
        $monlist = $monmodel->getAll();
        require_once __DIR__ . '../../views/index.php';
    }

    public function login()
    {
        $KHmodel = new TrangChu();
        $KHlist = $KHmodel->KhachHang();
        session_start();
        if ($_SERVER['REQUEST_METHOD'] =='POST') {
            $username = ($_POST['username']);
            $telephone = ($_POST['telephone']);
            var_dump($username);
            foreach ($KHlist as $kh) {
                if ($username ==$kh['Hoten'] && $telephone ==$kh['Sdt']) {
                    $_SESSION['username'] = $username;
                    $_SESSION['logged_in'] = true;
                    header('Location: /DO_An_WEB%20(2)/index.php?action=TrangChu');
                    exit();
                }
            }
            echo "Incorrect username or password!";
        }
        require_once __DIR__ . '../../views/login.php';
    }
    public function signup()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $db = new TrangChu();
            $name = trim($_POST['name']);
            $email = trim($_POST['email']);
            $sdt = trim($_POST['sdt']);
            $errors = [];
            if (empty($name)) {
                $errors[] = "Tên không được để trống.";
            } elseif (strlen($name) < 3) {
                $errors[] = "Tên phải có ít nhất 3 ký tự.";
            }
            if (empty($email)) {
                $errors[] = "Email không được để trống.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Email không đúng định dạng.";
            }
            if (empty($sdt)) {
                $errors[] = "Số điện thoại không được để trống.";
            } elseif (!preg_match('/^[0-9]{10,15}$/', $sdt)) {
                $errors[] = "Số điện thoại phải là dãy số có từ 10 đến 15 chữ số.";
            }
            if (empty($errors)) {
                $db->addKhadhang($email, $name, $sdt);
                header('Location: /DO_An_WEB%20(2)/index.php?action=login');
                exit;
            } else
                require_once __DIR__ . '../../views/signup.php';
        }
        require_once __DIR__ . '../../views/signup.php';
    }

    public function  GioiThieu()
    {
        session_start();
        if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {

            header("Location: views/GioiThieu.php");
            exit();
        } else {
            require_once __DIR__ . '../../views/GioiThieulogin.php';
        }
    }
    public function  TrangChu()
    {
        session_start();
        $monmodel = new TrangChu();
        $monlist = $monmodel->getAll();
        if (!isset($_SESSION['username']) || empty($_SESSION['username'])) {
            header('Location: /DO_An_WEB%20(2)/index.php');
            exit();
        } else {
            require_once __DIR__ . '../../views/TrangChu.php';
        }
    }
    public function  LogOut()
    {
        session_start();
        $_SESSION['username'] = null;
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        header('Location: /DO_An_WEB%20(2)/index.php');
        exit();
    }
    public function TimMaKH($makh)
    {
        $KHmodel = new TrangChu();
        $KHlist = $KHmodel->KhachHang();
        foreach ($KHlist as $kh) {
            if ($kh['Hoten'] == $makh) {
                $ma = $kh['MaKhachHang'];
                break;
            }
        }
        return $ma;
    }

    public function AddGioHang($Soluong, $maMA)
    {
        $monmodel = new TrangChu();
        $monlist = $monmodel->getAll();
        $loaimonModel = new loaimon();
        $loaimonList = $loaimonModel->getAll();
        session_start();
        $temp = 0;
        $username = $_SESSION['username'];
        $Makh = $this->TimMaKH($username);
        $giohangModel = new GioHang();
        $giohanglist = $giohangModel->getall();
        foreach ($giohanglist as $gh) {
            if ($gh['MaKhachHang'] == $Makh && $gh['MaMA'] == $maMA && $gh['MaTTDB'] == 0) {
                $temp = 1;
                $sl = $gh['Soluong'] + $Soluong;
                $giohangModel->update($sl, $maMA, $Makh);
                break;
            }
        }
        if ($temp == 0) {
            $giohangModel->addGH($Makh, $maMA, $Soluong);
        }

        require_once __DIR__ . '../../views/MonAn.php';
    }
    public function GioHang($temp, $time)
    {
        session_start();
        $username = $_SESSION['username'];
        $Makh = $this->TimMaKH($username);
        $giohangmodel = new GioHang();
        $GHlist = $giohangmodel->GetallGioHang($Makh);
        $HDlist = $giohangmodel->GetallHoaDon($Makh);
        if ($temp == 'giohang') {
            require_once __DIR__ . '../../views/GioHang.php';
        } else {
            require_once __DIR__ . '../../views/HoaDon.php';
        }
    }
    public  function removeGioHang($maMA)
    {
        session_start();
        $username = $_SESSION['username'];
        $Makh = $this->TimMaKH($username);
        $giohangmodel = new GioHang();
        $giohangmodel->removeGioHang($Makh, $maMA);
        header('Location: /DO_An_WEB%20(2)/index.php?action=GioHang&temp=giohang');
        exit();
    }

    public function getallBanAn()
    {
        session_start();
        $username = $_SESSION['username'];
        $Makh = $this->TimMaKH($username);
        $banmodel = new Ban();
        $banlist = $banmodel->getallBan();
        $banedit = new TrangChu();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $MaBan = $_POST['MaBan'] ?? '';
            $time = $_POST['time'] ?? '';
            $banedit = new TrangChu();
            $banedit->updateBan($MaBan, $Makh);
            header('Location: /DO_An_WEB%20(2)/index.php?action=GioHang&time=' . urlencode($time));

            exit();
        }
        require_once __DIR__ . '../../views/BanAn.php';
    }
    public function end($time)
    {
        session_start();
        $username = $_SESSION['username'];
        $Makh = $this->TimMaKH($username);
        $TTDB = new TrangChu();
        $TTDB->addThongTinDatBan($Makh, $time);
        $UpdateGioHang = new GioHang();
        $MaTTDBModel = new ThongTinDatBan();
        $MaTTDB = $MaTTDBModel->GetFirstTTDB($Makh);
        $UpdateGioHang->updateGioHang($MaTTDB, $Makh);
        require_once __DIR__ . '../../views/End.php';
    }
}
