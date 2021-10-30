<?php require_once('../Connections/conn.php'); ?><?php
//initialize the session
if (!isset($_SESSION)) {
  session_start();
}
date_default_timezone_set('asia/jakarta');
// ** Logout the current user. **
$logoutAction = $_SERVER['PHP_SELF']."?doLogout=true";
if ((isset($_SERVER['QUERY_STRING'])) && ($_SERVER['QUERY_STRING'] != "")){
  $logoutAction .="&". htmlentities($_SERVER['QUERY_STRING']);
}

if ((isset($_GET['doLogout'])) &&($_GET['doLogout']=="true")){
  //to fully log out a visitor we need to clear the session varialbles
  $_SESSION['MM_Username'] = NULL;
  $_SESSION['MM_UserGroup'] = NULL;
  $_SESSION['PrevUrl'] = NULL;
  unset($_SESSION['MM_Username']);
  unset($_SESSION['MM_UserGroup']);
  unset($_SESSION['PrevUrl']);
	
  $logoutGoTo = "../index.php";
  if ($logoutGoTo) {
    header("Location: $logoutGoTo");
    exit;
  }
}
?>
<?php
if (!isset($_SESSION)) {
  session_start();
}
$MM_authorizedUsers = "";
$MM_donotCheckaccess = "true";

// *** Restrict Access To Page: Grant or deny access to this page
function isAuthorized($strUsers, $strGroups, $UserName, $UserGroup) { 
  // For security, start by assuming the visitor is NOT authorized. 
  $isValid = False; 

  // When a visitor has logged into this site, the Session variable MM_Username set equal to their username. 
  // Therefore, we know that a user is NOT logged in if that Session variable is blank. 
  if (!empty($UserName)) { 
    // Besides being logged in, you may restrict access to only certain users based on an ID established when they login. 
    // Parse the strings into arrays. 
    $arrUsers = Explode(",", $strUsers); 
    $arrGroups = Explode(",", $strGroups); 
    if (in_array($UserName, $arrUsers)) { 
      $isValid = true; 
    } 
    // Or, you may restrict access to only certain users based on their username. 
    if (in_array($UserGroup, $arrGroups)) { 
      $isValid = true; 
    } 
    if (($strUsers == "") && true) { 
      $isValid = true; 
    } 
  } 
  return $isValid; 
}

$MM_restrictGoTo = "../index.php";
if (!((isset($_SESSION['MM_Username'])) && (isAuthorized("",$MM_authorizedUsers, $_SESSION['MM_Username'], $_SESSION['MM_UserGroup'])))) {   
  $MM_qsChar = "?";
  $MM_referrer = $_SERVER['PHP_SELF'];
  if (strpos($MM_restrictGoTo, "?")) $MM_qsChar = "&";
  if (isset($QUERY_STRING) && strlen($QUERY_STRING) > 0) 
  $MM_referrer .= "?" . $QUERY_STRING;
  $MM_restrictGoTo = $MM_restrictGoTo. $MM_qsChar . "accesscheck=" . urlencode($MM_referrer);
  header("Location: ". $MM_restrictGoTo); 
  exit;
}
//Log History Login
mysql_select_db($database_conn, $conn);
$query_log = sprintf("INSERT INTO tbl_log 
						SET 
						uname = '%s',
						path = '%s'", 
						mysql_real_escape_string($_SESSION['MM_Username']),
						mysql_real_escape_string($_SERVER['PHP_SELF']));
$log = mysql_query($query_log, $conn) or die(mysql_error());
//End Log History Login
?>
<?php
if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? "'" . doubleval($theValue) . "'" : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

$editFormAction = $_SERVER['PHP_SELF'];
if (isset($_SERVER['QUERY_STRING'])) {
  $editFormAction .= "?" . htmlentities($_SERVER['QUERY_STRING']);
}
		/*Ubah nama file gambar*/
	$temp = explode(".", $_FILES["fileskp"]["name"]);
	$nama_baru = round(microtime(true)). '.' . end($temp);

if ((isset($_POST["MM_update"])) && ($_POST["MM_update"] == "form")) {
		$max_size = 1024 * 2000;
		$filesize =$_FILES['fileskp']['size'];
		
		if($filesize <= $max_size) {
  $updateSQL = sprintf("UPDATE tbl_perus SET fileskp='$nama_baru', namal=%s, username=%s, password=%s, namapr=%s, alt=%s, pngjwb=%s, email=%s, npwp=%s, idskep=%s, noskep=%s, kombk=%s, komhp=%s, telp=%s, idaktif=%s, ket=%s, tgl=%s, thn=%s WHERE idper=%s",
                       GetSQLValueString($_POST['namal'], "text"),
                       GetSQLValueString($_POST['username'], "text"),
                       GetSQLValueString($_POST['password'], "text"),
                       GetSQLValueString($_POST['namapr'], "text"),
                       GetSQLValueString($_POST['alt'], "text"),
                       GetSQLValueString($_POST['pngjwb'], "text"),
                       GetSQLValueString($_POST['email'], "text"),
                       GetSQLValueString($_POST['npwp'], "text"),
                       GetSQLValueString($_POST['idskep'], "int"),
                       GetSQLValueString($_POST['noskep'], "text"),
                       GetSQLValueString($_POST['kombk'], "text"),
                       GetSQLValueString($_POST['komhp'], "text"),
                       GetSQLValueString($_POST['telp'], "text"),
                       GetSQLValueString($_POST['idaktif'], "int"),
                       GetSQLValueString($_POST['ket'], "text"),
                       GetSQLValueString($_POST['tgl'], "text"),
                       GetSQLValueString($_POST['thn'], "text"),
                       GetSQLValueString($_POST['idper'], "int"),
					   GetSQLValueString($_FILES['fileskp']['name'], "text"));

  mysql_select_db($database_conn, $conn);
  $Result1 = mysql_query($updateSQL, $conn) or die(mysql_error());
  if(isset($_FILES['fileskp'])){
  move_uploaded_file($_FILES['fileskp']['tmp_name'],'../fileskep/'.$nama_baru);

  $updateGoTo = "Home.php";
  if (isset($_SERVER['QUERY_STRING'])) {
    $updateGoTo .= (strpos($updateGoTo, '?')) ? "&" : "?";
    $updateGoTo .= $_SERVER['QUERY_STRING'];
  }
  header(sprintf("Location: %s", $updateGoTo));
}
		}
		else {
			echo "<script>alert(\"Ukuran File Melebihi 2 Mb\");</script>";
			}
}

mysql_select_db($database_conn, $conn);
$query_rsskep = "SELECT * FROM tbl_skep";
$rsskep = mysql_query($query_rsskep, $conn) or die(mysql_error());
$row_rsskep = mysql_fetch_assoc($rsskep);
$totalRows_rsskep = mysql_num_rows($rsskep);

mysql_select_db($database_conn, $conn);
$query_rsaktif = "SELECT * FROM tbl_aktif";
$rsaktif = mysql_query($query_rsaktif, $conn) or die(mysql_error());
$row_rsaktif = mysql_fetch_assoc($rsaktif);
$totalRows_rsaktif = mysql_num_rows($rsaktif);

mysql_select_db($database_conn, $conn);
$query_rsthn1 = "SELECT * FROM tbl_thn";
$rsthn1 = mysql_query($query_rsthn1, $conn) or die(mysql_error());
$row_rsthn1 = mysql_fetch_assoc($rsthn1);
$totalRows_rsthn1 = mysql_num_rows($rsthn1);

$colname_rsvw = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_rsvw = $_SESSION['MM_Username'];
}
mysql_select_db($database_conn, $conn);
$query_rsvw = sprintf("SELECT * FROM tbl_perus WHERE username = %s", GetSQLValueString($colname_rsvw, "text"));
$rsvw = mysql_query($query_rsvw, $conn) or die(mysql_error());
$row_rsvw = mysql_fetch_assoc($rsvw);
$totalRows_rsvw = mysql_num_rows($rsvw);

$colname_rspru = "-1";
if (isset($_SESSION['MM_Username'])) {
  $colname_rspru = $_SESSION['MM_Username'];
}
mysql_select_db($database_conn, $conn);
$query_rspru = sprintf("SELECT * FROM tbl_perus WHERE username = %s", GetSQLValueString($colname_rspru, "text"));
$rspru = mysql_query($query_rspru, $conn) or die(mysql_error());
$row_rspru = mysql_fetch_assoc($rspru);
$totalRows_rspru = mysql_num_rows($rspru);

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title>
    Home | SiPOLan
  </title>
  <meta content='width=device-width, initial-scale=1.0, shrink-to-fit=no' name='viewport' />
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700|Roboto+Slab:400,700|Material+Icons" />
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
  <!-- CSS Files -->
  <link href="../assets/css/material-dashboard.css?v=2.2.2" rel="stylesheet" />
  <!-- CSS Just for demo purpose, don't include it in your project -->
  <link href="../assets/demo/demo.css" rel="stylesheet" />
</head>

<body class="">
  <div class="wrapper ">
    <div class="sidebar" data-color="azure" data-background-color="red" data-image="../assets/img/sidebar-2.jpg">
      <!--
        Tip 1: You can change the color of the sidebar using: data-color="purple | azure | green | orange | danger"

        Tip 2: you can also add an image using data-image tag
    -->
      <div class="logo"><a href="" class="simple-text logo-mini">
          <img src="../img/logo.png" class="img-responsive" style="width:30px;height:30px;">	
        </a>
        <a href="" class="simple-text logo-normal">
          <strong>KPPBC MEDAN</strong>
        </a></div>
      <div class="sidebar-wrapper">
        <div class="user">
          <div class="photo">
           <img src="../img/kb2.png" />
          </div>
          <div class="user-info">
            <a data-toggle="collapse" href="#collapseExample" class="username">
              <span>
               <strong>Welcome, <?php echo $row_rsvw['username']; ?></strong>
                <b class="caret"></b>
              </span>
            </a>
            <div class="collapse" id="collapseExample">
              <ul class="nav">
                <li class="nav-item">
                  <a class="nav-link" href="../admin_app/hal_update_psw.php">
                    <span class="sidebar-mini"> SP </span>
                    <span class="sidebar-normal"> Setting Password </span>
                  </a>
                 </li>
              </ul>
            </div>
          </div>
        </div>
        <ul class="nav">
          <li class="nav-item active ">
            <a class="nav-link" href="../user_app/home.php">
              <i class="material-icons">dashboard</i>
              <p> <strong>Dashboard </strong></p>
            </a>
          </li>
          <li class="nav-item ">
            <a class="nav-link" href="hal_profil.php">
              <i class="material-icons">image</i>
              <p> <strong>PROFIL PERUSAHAAN</strong>
              </p>
            </a>
            <li class="nav-item ">
            <a class="nav-link" data-toggle="collapse" href="#componentsExamples">
              <i class="material-icons">apps</i>
              <p> <strong>LAYANAN SURAT</strong>
                <b class="caret"></b>
              </p>
            </a>
           <div class="collapse" id="componentsExamples">
              <ul class="nav">
                <li class="nav-item ">
                  <a class="nav-link" href="hal_surat_masuk.php">
                    <span class="sidebar-mini"> SM </span>
                    <span class="sidebar-normal"><strong>Surat Masuk</strong> </span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="hal_surat_keluar.php">
                    <span class="sidebar-mini"> SK </span>
                    <span class="sidebar-normal"><strong>Surat Keluar</strong> </span>
                  </a>
                </li>
                <li class="nav-item ">
                  <a class="nav-link" href="hal_janji_layanan.php">
                    <span class="sidebar-mini"> JL </span>
                    <span class="sidebar-normal"><strong>Data Janji Layanan</strong> </span>
                  </a>
                </li>
              </ul>
            </div>
          </li>
          <li class="nav-item ">
            <a class="nav-link" data-toggle="collapse" href="#formsExamples">
              <i class="material-icons">content_paste</i>
              <p> <strong>LAYANAN KLInIK</strong>
                <b class="caret"></b>
              </p>
            </a>
            <div class="collapse" id="formsExamples">
              <ul class="nav">
                <li class="nav-item ">
                  <a class="nav-link" href="hal_online.php">
                    <span class="sidebar-mini"> KL </span>
                    <span class="sidebar-normal"> <strong>KLInIK Online</strong> </span>
                  </a>
                </li>
                </ul>
            </div>
          </li>
         </div>
      <div class="sidebar-background"></div>
    </div>
    <div class="main-panel">
      <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-transparent navbar-absolute fixed-top ">
        <div class="container-fluid">
          <div class="navbar-wrapper">
            <div class="navbar-minimize">
              <button id="minimizeSidebar" class="btn btn-just-icon btn-white btn-fab btn-round">
                <i class="material-icons text_align-center visible-on-sidebar-regular">more_vert</i>
                <i class="material-icons design_bullet-list-67 visible-on-sidebar-mini">view_list</i>
              </button>
            </div>
            <a class="navbar-brand" ><h3><marquee><strong> SiPOLan (Sistem Pelayanan Online)</strong></marquee></h3> </a>
          </div>
          <button class="navbar-toggler" type="button" data-toggle="collapse" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
            <span class="sr-only">Toggle navigation</span>
            <span class="navbar-toggler-icon icon-bar"></span>
            <span class="navbar-toggler-icon icon-bar"></span>
            <span class="navbar-toggler-icon icon-bar"></span>
          </button>
          <div class="collapse navbar-collapse justify-content-end">
            <form class="navbar-form">
              <div class="input-group no-border">
                <input type="text" value="" class="form-control" placeholder="Search...">
                <button type="submit" class="btn btn-white btn-round btn-just-icon">
                  <i class="material-icons">search</i>
                  <div class="ripple-container"></div>
                </button>
              </div>
            </form>
            <ul class="navbar-nav">
              <li class="nav-item">
                <a class="nav-link" href="javascript:;">
                  <i class="material-icons">dashboard</i>
                  <p class="d-lg-none d-md-block">
                    Stats
                  </p>
                </a>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link" href="http://example.com" id="navbarDropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="material-icons">notifications</i>
                  <p class="d-lg-none d-md-block">
                    Some Actions
                  </p>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownMenuLink">
                </div>
              </li>
              <li class="nav-item dropdown">
                <a class="nav-link" href="javascript:;" id="navbarDropdownProfile" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="material-icons">person</i>
                  <p class="d-lg-none d-md-block">
                    Account
                  </p>
                </a>
                <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdownProfile">
                  <a class="dropdown-item" href="#"></a>
                  <a class="dropdown-item" href="#"></a>
                  <div class="dropdown-divider"></div>
                  <a class="dropdown-item" href="<?php echo $logoutAction ?>">Log out</a>
                </div>
              </li>
            </ul>
          </div>
        </div>
      </nav>
      <!-- End Navbar -->
      <div class="content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-md-12">
              <div class="card">
                <div class="card-header card-header-primary card-header-icon">
                  <div class="card-icon">
                    <i class="material-icons">assignment</i>
                  </div>
                  <h4 class="card-title"><strong>DATA PROFIL PERUSAHAAN</strong></h4>
                  </div>
                <div class="card-body">
                <div class="panel-body">
                            
							<form method="POST" action="<?php echo $editFormAction; ?>" name="form" enctype="multipart/form-data">
							  <div class="table-responsive">
							    <table class="table table-bordered table-striped">
							      <thead>
						          </thead>
							      <tbody>
							        
							        <tr>
							          <th width="20%">Nama Perusahaan</th>
                                      <td width="2%">:</td>
                                        <td width="78%"><input name="namapr" type="text" id="namapr" value="<?php echo $row_rspru['namapr']; ?>" size="96"></td>
                                    </tr>
							        <tr>
							          <th>Alamat Perusahaan</th>
                                      <td>:</td>
                                      <td><input name="alt" type="text" id="alt" value="<?php echo $row_rspru['alt']; ?>" size="96"></td>
                                    </tr>
							        <tr>
							          <th>Email perusahaan</th>
                                      <td>:</td>
                                      <td><input name="email" type="text" id="email" value="<?php echo $row_rspru['email']; ?>" size="96"></td>
                                    </tr>
							        <tr>
							          <th>NPWP</th>
                                      <td>:</td>
                                      <td><input name="npwp" type="text" id="npwp" value="<?php echo $row_rspru['npwp']; ?>" size="96"></td>
                                    </tr>
							        <tr>
							          <th>Penanggung Jawab</th>
							          <td>:</td>
							          <td><input name="pngjwb" type="text" id="pngjwb" value="<?php echo $row_rspru['pngjwb']; ?>" size="96"></td>
						            </tr>
							        <tr>
							          <th>Jenis SKEP</th>
                                      <td>:</td>
                                      <td><select name="idskep" id="idskep">
                                        <option value="Pilih">Pilih</option>
                                        <?php do { ?>
                                        <option value="<?php echo $row_rsskep['idskep']?>"><?php echo $row_rsskep['namas']?></option>
                                        <?php
                                        } while ($row_rsskep = mysql_fetch_assoc($rsskep));
                                        $rows = mysql_num_rows($rsskep);
                                        if($rows > 0) {
                                        mysql_data_seek($rsskep, 0);
	                                    $row_rsskep = mysql_fetch_assoc($rsskep);
                                         }?>
                                      </select> <?php echo $row_rsvw['namas']; ?></td>
                                    </tr>
							        <tr>
							          <th>Nomor SKEP Terbaru</th>
                                      <td>:</td>
                                      <td><input name="noskep" type="text" id="noskep" value="<?php echo $row_rsvw['noskep']; ?>" size="96"></td>
                                    </tr>
							        <tr>
							          <th>Upload Nomor SKEP / IU / IUI Terbaru</th>
                                      <td>:</td>
                                      <td><input type="file" name="fileskp" id="fileskp">
                                      Scan menjadi satu halaman (pdf) : SKEP, NIB dan Izin Usaha Industri</td>
                                    </tr>
							        <tr>
							          <th>Bahan Baku</th>
                                      <td>:</td>
                                      <td><textarea name="kombk" cols="97" id="kombk"><?php echo $row_rspru['kombk']; ?></textarea></td>
                                    </tr>
							        <tr>
							          <th>Hasil Produksi</th>
                                      <td>:</td>
                                      <td><textarea name="komhp" cols="97" id="komhp"><?php echo $row_rspru['komhp']; ?></textarea></td>
                                    </tr>
							        <tr>
							          <th>Nomor Telepon / HP</th>
                                      <td>:</td>
                                      <td><input name="telp" type="text" id="telp" value="<?php echo $row_rspru['telp']; ?>" size="50"></td>
                                    </tr>
							        <tr>
							          <th>Status Perusahaan</th>
                                      <td>:</td>
                                      <td><select name="idaktif" id="idaktif">
                                        <option value="Pilih">Pilih</option>
                                        <?php do { ?>
                                        <option value="<?php echo $row_rsaktif['idaktif']?>"><?php echo $row_rsaktif['namaa']?></option>
                                        <?php
} while ($row_rsaktif = mysql_fetch_assoc($rsaktif));
  $rows = mysql_num_rows($rsaktif);
  if($rows > 0) {
      mysql_data_seek($rsaktif, 0);
	  $row_rsaktif = mysql_fetch_assoc($rsaktif);
  }
?>
                                      </select>
                                      <?php echo $row_rsvw['namaa']; ?></td>
                                    </tr>
							        <tr>
							          <th>Keterangan</th>
                                      <td>:</td>
                                      <td><textarea name="ket" cols="97" id="ket"><?php echo $row_rspru['ket']; ?></textarea>
                                      </td>
                                    </tr>
							        <tr>
							          <th><span class="style1"><a href="Home.php"class="btn btn-info btn-xs">Kembali</a></span></th>
                                      <td>&nbsp;</td>
                                      <td><input type="submit" name="button" id="button" value="Simpan">
                                      <input name="idper" type="hidden" id="idper" value="<?php echo $row_rspru['idper']; ?>">
                                      <input name="thn" type="hidden" id="thn" value="<?php echo $row_rsthn1['thn']; ?>">
                                      <input type="hidden" name="tgl" id="tgl" value="<?
                                      $tgl=date ('d-M-Y');
									  echo $tgl;
									  ?>">
                                      <input name="namal" type="hidden" id="namal" value="<?php echo $row_rspru['namal']; ?>">
                                      <input name="username" type="hidden" id="username" value="<?php echo $row_rspru['username']; ?>">
                                      <input name="password" type="hidden" id="password" value="<?php echo $row_rspru['password']; ?>"></td>
                                    </tr>
						          </tbody>
						        </table>
                              </div>
						      <input type="hidden" name="MM_update" value="form">
						  </form>
					  </div>
                    </div>
                </div>
                <!-- end content-->
              </div>
              <!--  end card  -->
            </div>
            <!-- end col-md-12 -->
          </div>
          <!-- end row -->
        </div>
      </div>
      <footer class="footer">
        <div class="container-fluid">
          <nav class="float-left">
            <ul>
              <li>
                <a href="https://bcmedan.beacukai.go.id"></a>
                  <h4><strong>LINK WEBSITE BC MEDAN</strong></h4>
                  </li>
                  <a href="https://www.facebook.com/beacukaimedan" class="btn btn-just-icon btn-link btn-info">
                      <i class="fa fa-facebook-square"></i>
                  </a>
                  <a href="https://www.instagram.com/beacukaimedan" class="btn btn-just-icon btn-link btn-danger">
                      <i class="fa fa-instagram"></i>
                  </a>
                  <a href="https://www.twitter.com/bcmedan" class="btn btn-just-icon btn-link btn-primary">
                      <i class="fa fa-twitter"></i>
                 </a>
             </ul>
          </nav>
          <div class="copyright float-right">
            &copy;
            <script>
              document.write(new Date().getFullYear())
            </script>, Created <i class="material-icons">favorite</i> by KFR & TIM IT BC MEDAN
            </div>
        </div>
      </footer>
    </div>
  </div>
  <div class="fixed-plugin">
    <div class="dropdown show-dropdown">
      <a href="#" data-toggle="dropdown">
        <i class="fa fa-cog fa-2x"> </i>
      </a>
      <ul class="dropdown-menu">
        <li class="header-title"> Sidebar Filters</li>
        <li class="adjustments-line">
          <a href="javascript:void(0)" class="switch-trigger active-color">
            <div class="badge-colors ml-auto mr-auto">
              <span class="badge filter badge-purple" data-color="purple"></span>
              <span class="badge filter badge-azure" data-color="azure"></span>
              <span class="badge filter badge-green" data-color="green"></span>
              <span class="badge filter badge-warning" data-color="orange"></span>
              <span class="badge filter badge-danger" data-color="danger"></span>
              <span class="badge filter badge-rose active" data-color="rose"></span>
            </div>
            <div class="clearfix"></div>
          </a>
        </li>
        <li class="header-title">Sidebar Background</li>
        <li class="adjustments-line">
          <a href="javascript:void(0)" class="switch-trigger background-color">
            <div class="ml-auto mr-auto">
              <span class="badge filter badge-black active" data-background-color="black"></span>
              <span class="badge filter badge-white" data-background-color="white"></span>
              <span class="badge filter badge-red" data-background-color="red"></span>
            </div>
            <div class="clearfix"></div>
          </a>
        </li>
        <li class="adjustments-line">
          <a href="javascript:void(0)" class="switch-trigger">
            <p>Sidebar Mini</p>
            <label class="ml-auto">
              <div class="togglebutton switch-sidebar-mini">
                <label>
                  <input type="checkbox">
                  <span class="toggle"></span>
                </label>
              </div>
            </label>
            <div class="clearfix"></div>
          </a>
        </li>
        <li class="adjustments-line">
          <a href="javascript:void(0)" class="switch-trigger">
            <p>Sidebar Images</p>
            <label class="switch-mini ml-auto">
              <div class="togglebutton switch-sidebar-image">
                <label>
                  <input type="checkbox" checked="">
                  <span class="toggle"></span>
                </label>
              </div>
            </label>
            <div class="clearfix"></div>
          </a>
        </li>
        <li class="header-title">Images</li>
        <li class="active">
          <a class="img-holder switch-trigger" href="javascript:void(0)">
            <img src="../assets/img/sidebar-1.jpg" alt="">
          </a>
        </li>
        <li>
          <a class="img-holder switch-trigger" href="javascript:void(0)">
            <img src="../assets/img/sidebar-2.jpg" alt="">
          </a>
        </li>
        <li>
          <a class="img-holder switch-trigger" href="javascript:void(0)">
            <img src="../assets/img/sidebar-3.jpg" alt="">
          </a>
        </li>
        <li>
          <a class="img-holder switch-trigger" href="javascript:void(0)">
            <img src="../assets/img/sidebar-4.jpg" alt="">
          </a>
        </li>
        <li class="button-container">
        </li>
        <li class="button-container github-star">
        </li>
        <li class="button-container text-center">
          <br>
          <br>
        </li>
      </ul>
    </div>
  </div>
  <!--   Core JS Files   -->
  <script src="../assets/js/core/jquery.min.js"></script>
  <script src="../assets/js/core/popper.min.js"></script>
  <script src="../assets/js/core/bootstrap-material-design.min.js"></script>
  <script src="../assets/js/plugins/perfect-scrollbar.min.js"></script>
  <!-- Plugin for the momentJs  -->
  <script src="../assets/js/plugins/moment.min.js"></script>
  <!--  Plugin for Sweet Alert -->
  <script src="../assets/js/plugins/sweetalert2.js"></script>
  <!-- Forms Validations Plugin -->
  <script src="../assets/js/plugins/jquery.validate.min.js"></script>
  <!-- Plugin for the Wizard, full documentation here: https://github.com/VinceG/twitter-bootstrap-wizard -->
  <script src="../assets/js/plugins/jquery.bootstrap-wizard.js"></script>
  <!--	Plugin for Select, full documentation here: http://silviomoreto.github.io/bootstrap-select -->
  <script src="../assets/js/plugins/bootstrap-selectpicker.js"></script>
  <!--  Plugin for the DateTimePicker, full documentation here: https://eonasdan.github.io/bootstrap-datetimepicker/ -->
  <script src="../assets/js/plugins/bootstrap-datetimepicker.min.js"></script>
  <!--  DataTables.net Plugin, full documentation here: https://datatables.net/  -->
  <script src="../assets/js/plugins/jquery.dataTables.min.js"></script>
  <!--	Plugin for Tags, full documentation here: https://github.com/bootstrap-tagsinput/bootstrap-tagsinputs  -->
  <script src="../assets/js/plugins/bootstrap-tagsinput.js"></script>
  <!-- Plugin for Fileupload, full documentation here: http://www.jasny.net/bootstrap/javascript/#fileinput -->
  <script src="../assets/js/plugins/jasny-bootstrap.min.js"></script>
  <!--  Full Calendar Plugin, full documentation here: https://github.com/fullcalendar/fullcalendar    -->
  <script src="../assets/js/plugins/fullcalendar.min.js"></script>
  <!-- Vector Map plugin, full documentation here: http://jvectormap.com/documentation/ -->
  <script src="../assets/js/plugins/jquery-jvectormap.js"></script>
  <!--  Plugin for the Sliders, full documentation here: http://refreshless.com/nouislider/ -->
  <script src="../assets/js/plugins/nouislider.min.js"></script>
  <!-- Include a polyfill for ES6 Promises (optional) for IE11, UC Browser and Android browser support SweetAlert -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/core-js/2.4.1/core.js"></script>
  <!-- Library for adding dinamically elements -->
  <script src="../assets/js/plugins/arrive.min.js"></script>
  <!--  Google Maps Plugin    -->
  <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script>
  <!-- Chartist JS -->
  <script src="../assets/js/plugins/chartist.min.js"></script>
  <!--  Notifications Plugin    -->
  <script src="../assets/js/plugins/bootstrap-notify.js"></script>
  <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
  <script src="../assets/js/material-dashboard.js?v=2.2.2" type="text/javascript"></script>
  <!-- Material Dashboard DEMO methods, don't include it in your project! -->
  <script>
    $(document).ready(function() {
      $().ready(function() {
        $sidebar = $('.sidebar');

        $sidebar_img_container = $sidebar.find('.sidebar-background');

        $full_page = $('.full-page');

        $sidebar_responsive = $('body > .navbar-collapse');

        window_width = $(window).width();

        fixed_plugin_open = $('.sidebar .sidebar-wrapper .nav li.active a p').html();

        if (window_width > 767 && fixed_plugin_open == 'Dashboard') {
          if ($('.fixed-plugin .dropdown').hasClass('show-dropdown')) {
            $('.fixed-plugin .dropdown').addClass('open');
          }

        }

        $('.fixed-plugin a').click(function(event) {
          // Alex if we click on switch, stop propagation of the event, so the dropdown will not be hide, otherwise we set the  section active
          if ($(this).hasClass('switch-trigger')) {
            if (event.stopPropagation) {
              event.stopPropagation();
            } else if (window.event) {
              window.event.cancelBubble = true;
            }
          }
        });

        $('.fixed-plugin .active-color span').click(function() {
          $full_page_background = $('.full-page-background');

          $(this).siblings().removeClass('active');
          $(this).addClass('active');

          var new_color = $(this).data('color');

          if ($sidebar.length != 0) {
            $sidebar.attr('data-color', new_color);
          }

          if ($full_page.length != 0) {
            $full_page.attr('filter-color', new_color);
          }

          if ($sidebar_responsive.length != 0) {
            $sidebar_responsive.attr('data-color', new_color);
          }
        });

        $('.fixed-plugin .background-color .badge').click(function() {
          $(this).siblings().removeClass('active');
          $(this).addClass('active');

          var new_color = $(this).data('background-color');

          if ($sidebar.length != 0) {
            $sidebar.attr('data-background-color', new_color);
          }
        });

        $('.fixed-plugin .img-holder').click(function() {
          $full_page_background = $('.full-page-background');

          $(this).parent('li').siblings().removeClass('active');
          $(this).parent('li').addClass('active');


          var new_image = $(this).find("img").attr('src');

          if ($sidebar_img_container.length != 0 && $('.switch-sidebar-image input:checked').length != 0) {
            $sidebar_img_container.fadeOut('fast', function() {
              $sidebar_img_container.css('background-image', 'url("' + new_image + '")');
              $sidebar_img_container.fadeIn('fast');
            });
          }

          if ($full_page_background.length != 0 && $('.switch-sidebar-image input:checked').length != 0) {
            var new_image_full_page = $('.fixed-plugin li.active .img-holder').find('img').data('src');

            $full_page_background.fadeOut('fast', function() {
              $full_page_background.css('background-image', 'url("' + new_image_full_page + '")');
              $full_page_background.fadeIn('fast');
            });
          }

          if ($('.switch-sidebar-image input:checked').length == 0) {
            var new_image = $('.fixed-plugin li.active .img-holder').find("img").attr('src');
            var new_image_full_page = $('.fixed-plugin li.active .img-holder').find('img').data('src');

            $sidebar_img_container.css('background-image', 'url("' + new_image + '")');
            $full_page_background.css('background-image', 'url("' + new_image_full_page + '")');
          }

          if ($sidebar_responsive.length != 0) {
            $sidebar_responsive.css('background-image', 'url("' + new_image + '")');
          }
        });

        $('.switch-sidebar-image input').change(function() {
          $full_page_background = $('.full-page-background');

          $input = $(this);

          if ($input.is(':checked')) {
            if ($sidebar_img_container.length != 0) {
              $sidebar_img_container.fadeIn('fast');
              $sidebar.attr('data-image', '#');
            }

            if ($full_page_background.length != 0) {
              $full_page_background.fadeIn('fast');
              $full_page.attr('data-image', '#');
            }

            background_image = true;
          } else {
            if ($sidebar_img_container.length != 0) {
              $sidebar.removeAttr('data-image');
              $sidebar_img_container.fadeOut('fast');
            }

            if ($full_page_background.length != 0) {
              $full_page.removeAttr('data-image', '#');
              $full_page_background.fadeOut('fast');
            }

            background_image = false;
          }
        });

        $('.switch-sidebar-mini input').change(function() {
          $body = $('body');

          $input = $(this);

          if (md.misc.sidebar_mini_active == true) {
            $('body').removeClass('sidebar-mini');
            md.misc.sidebar_mini_active = false;

            if ($(".sidebar").length != 0) {
              var ps = new PerfectScrollbar('.sidebar');
            }
            if ($(".sidebar-wrapper").length != 0) {
              var ps1 = new PerfectScrollbar('.sidebar-wrapper');
            }
            if ($(".main-panel").length != 0) {
              var ps2 = new PerfectScrollbar('.main-panel');
            }
            if ($(".main").length != 0) {
              var ps3 = new PerfectScrollbar('main');
            }

          } else {

            if ($(".sidebar").length != 0) {
              var ps = new PerfectScrollbar('.sidebar');
              ps.destroy();
            }
            if ($(".sidebar-wrapper").length != 0) {
              var ps1 = new PerfectScrollbar('.sidebar-wrapper');
              ps1.destroy();
            }
            if ($(".main-panel").length != 0) {
              var ps2 = new PerfectScrollbar('.main-panel');
              ps2.destroy();
            }
            if ($(".main").length != 0) {
              var ps3 = new PerfectScrollbar('main');
              ps3.destroy();
            }


            setTimeout(function() {
              $('body').addClass('sidebar-mini');

              md.misc.sidebar_mini_active = true;
            }, 300);
          }

          // we simulate the window Resize so the charts will get updated in realtime.
          var simulateWindowResize = setInterval(function() {
            window.dispatchEvent(new Event('resize'));
          }, 180);

          // we stop the simulation of Window Resize after the animations are completed
          setTimeout(function() {
            clearInterval(simulateWindowResize);
          }, 1000);

        });
      });
    });
  </script>
  <script>
    $(document).ready(function() {
      $('#datatables').DataTable({
        "pagingType": "full_numbers",
        "lengthMenu": [
          [10, 25, 50, -1],
          [10, 25, 50, "All"]
        ],
        responsive: true,
        language: {
          search: "INPUT",
          searchPlaceholder: "Search records",
        }
      });

      var table = $('#datatables').DataTable();

      // Edit record

      table.on('click', '.edit', function() {
        $tr = $(this).closest('tr');

        if ($($tr).hasClass('child')) {
          $tr = $tr.prev('.parent');
        }

        var data = table.row($tr).data();
        alert('You press on Row: ' + data[0] + ' ' + data[1] + ' ' + data[2] + '\'s row.');
      });

      // Delete a record

      table.on('click', '.remove', function(e) {
        $tr = $(this).closest('tr');

        if ($($tr).hasClass('child')) {
          $tr = $tr.prev('.parent');
        }

        table.row($tr).remove().draw();
        e.preventDefault();
      });

      //Like record

      table.on('click', '.like', function() {
        alert('You clicked on Like button');
      });
    });
  </script>
</body>

</html>
<?php

mysql_free_result($rsskep);

mysql_free_result($rsaktif);

mysql_free_result($rsthn1);

mysql_free_result($rsvw);

mysql_free_result($rspru);

?>