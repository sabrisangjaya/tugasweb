<?php
//Page handler
if (isset($_GET['act']) && $_GET['act'] != '') {
	if (($_GET['act']=="edit") || ($_GET['act']=="tambah")){
		menu_halaman();
		editdata($con);
	}
	else  if($_GET['act']=="view"&&isset($_GET['nim'])){
		menu_halaman();
		detaildata($con);
	}
	else  if($_GET['act']=="view")default_halaman($con);
	else if ($_GET['act']=="logout"){
	    ?>
	     <script> location.replace("<?php echo $_SERVER['PHP_SELF'];?>"); </script>
		<?php
		$_SESSION['admindata'] = 0;
	}
	else if ($_GET['act']=="hapus")hapusdata($con);
	else if ($_GET['act']=="cari"){
		menu_halaman();
		searchdata($con);
	} else default_halaman($con);
} else menu_halaman();
//fungsi default halaman	
function default_halaman($con){
	menu_halaman();
	searchdata($con);
	showdata($con);
}
function menu_halaman(){
	echo '<a href="'.$_SERVER['PHP_SELF'].'?act=view">Lihat Data</a>';echo " | ";
	echo ' <a href="'.$_SERVER['PHP_SELF'].'?act=tambah">Tambah Data</a> ';echo " | ";
	echo ' <a href="'.$_SERVER['PHP_SELF'].'?act=cari">Cari Data</a>';echo " | ";
	echo ' <a href="'.$_SERVER['PHP_SELF'].'?act=logout">Log out</a> ';echo " | <br/><br/>";
}	
//Fungsi hapus data
function hapusdata($con){
	if(isset($_GET['nim'])&&$_GET['nim'] != ''){
		$nim=$_GET['nim'];
		$sql="DELETE FROM mahasiswa WHERE nim like '$nim'";
		$query = mysqli_query($con,$sql);
		if($query)echo "<script>history.go(-1);</script>";
		else echo "NIM tidak ditemukan. <a href='.'>kembali</a>";
	} else echo "NIM tidak ditemukan. <a href='.'>kembali</a>";
}
//Akhir Fungsi
//Fungsi lihat data default
function showdata($con){
	$sql="SELECT * FROM mahasiswa";
	datatabel($con,$sql);
}
//Akhir Fungsi
//Fungsi detail data
function detaildata($con){
	$sql="SELECT * FROM mahasiswa WHERE nim like'".$_GET['nim']."'";
	$query = mysqli_query($con,$sql);
	$data = mysqli_fetch_array($query);
	echo "<table class='datamahasiswa'>";
	if(mysqli_num_rows($query)>0){
		echo "<tr><td>Nama</td><td>". $data['nama']."</td></tr>";
		echo "<tr><td>NIM</td><td>". $data['nim']."</td></tr>";
		echo "<tr><td>Tanggal lahir</td><td>". $data['ttl']."</td></tr>";
		echo "<tr><td>Alamat</td><td>". $data['alamat']."</td></tr>";
	}
	echo "</table><a href='javascript:history.back()'>Kembali</a>";
}
//Akhir Fungsi
//Fungsi pencarian data
function searchdata($con){
	?>
	<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="GET">
		<input type="hidden" name="act" value="cari">
		<input type="text" name="q" placeholder="cari" value="<?php echo isset($_GET['q'])?$_GET['q']:'';?>">
		<button type="submit">Cari</button>
	</form>
	<?php
	if(isset($_GET['q'])){	
		$sql="SELECT * FROM mahasiswa WHERE nama like '%".$_GET['q']."%' OR alamat like '%".$_GET['q']."%'";
		datatabel($con,$sql);
	}
}
//Akhir Fungsi
//Fungsi menampilkan data untuk pencarian data dan lihat data
function datatabel($con,$sql){
$page=isset($_GET["page"])?$_GET["page"]:1;
$perhalaman=isset($_GET["jml"])?$_GET["jml"]:5;
$jumlahhalaman=ceil(mysqli_num_rows(mysqli_query($con,$sql))/$perhalaman);
$mulai=($page-1)*$perhalaman;
if (isset($_GET["orderby"])) $orderby= $_GET["orderby"]; else $orderby="nama"; 
if (isset($_GET["sort"])) $sort= $_GET["sort"]; else $sort="ASC"; 
$tambahan=" ";
if(isset($_GET["orderby"])&&isset($_GET["sort"])) $tambahan .=" ORDER BY ".$orderby." ".$sort;
$tambahan .=" LIMIT $mulai,".$perhalaman;
$query = mysqli_query($con,$sql.$tambahan);
if(mysqli_num_rows($query)>0){
		?>
	<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="GET">
		<?php
		if(isset($_GET['act'])&&$_GET['act']=="cari"){
			?>
			<input type="hidden" name="act" value="cari">
			<input type="hidden" name="q" value="<?php echo isset($_GET['q'])?$_GET['q']:'';?>">
			<?php
		}
		if(!(isset($_GET['act'])&&$_GET['act']=="view"&&isset($_GET['nim']))){
			?>
			Jumlah Data per halaman : <select name="jml" onchange="this.form.submit()">
			<?php
			$arr = array(5,10,15,20,25,50);
			if(!in_array($_GET["jml"], $arr)) echo "<option value='".$_GET["jml"]."' selected='selected'>".$_GET["jml"]."</option>";
			foreach ($arr as $i) {
				echo "<option value='".$i."'";
				if(isset($_GET["jml"])){
					if($_GET["jml"]==$i) echo " selected='selected'";
				}
				echo ">".$i."</option>";
			}
			?>
		</select>
		<?php
		if(!(strpos($_SERVER['REQUEST_URI'],'?act='))||$_GET['act']=="view"){
			?>
			<input type="hidden" name="act" value="view">
			<?php
		}
		?>
	</form>
	<?php
	}
	echo (isset($_GET['act'])&&$_GET['act']=="cari")?"Hasil :".mysqli_num_rows(mysqli_query($con,$sql))."":"";
	echo "<table class='datamahasiswa'><tr>";
	$arr = array("Nama","NIM","TTL","Alamat");
	echo "<th>No.</th>";
	foreach ($arr as $i) {
		if(isset($_GET['orderby'])&&$_GET['orderby']==$i&&isset($_GET['sort'])&&$_GET['sort']=="ASC")$actsort="DESC";
		else if(isset($_GET['orderby'])&&$_GET['orderby']==$i&&isset($_GET['sort'])&&$_GET['sort']=="DESC")$actsort="ASC";
		else if(isset($_GET['orderby'])&&$_GET['orderby']!=$i&&isset($_GET['sort'])&&$_GET['sort']=="ASC")$actsort="ASC";
		else if(isset($_GET['orderby'])&&$_GET['orderby']!=$i&&isset($_GET['sort'])&&$_GET['sort']=="DESC")$actsort="DESC";
		else $actsort="ASC";
		if (count($_GET)==0) echo "<th><a href='".$_SERVER['REQUEST_URI']."?orderby=".$i."&sort=ASC'>".$i."</a></th>";
		if(isset($_GET['orderby'])||isset($_GET['sort'])){	
			$link= $_SERVER['REQUEST_URI'];
			$link = preg_replace('~orderby=[^&]*~',"orderby=".$i,$link);
			$link = preg_replace('~(\?|&)sort=[^&]*~',"&sort=".$actsort,$link);
			echo "<th><a href='".$link."'>".$i;
			echo ($actsort=="ASC")?" &#x25B2;":" &#x25BC;";
			echo "</a></th>";
		}
		else if (count($_GET)>0)echo "<th><a href='".$_SERVER['REQUEST_URI']."&orderby=".$i."&sort=".$actsort."'>".$i."</a></th>";
	}
	echo "<th colspan='3'>Action</th></tr>";
	$nomer=$mulai+1;
	while($data = mysqli_fetch_array($query)) {
		echo "<tr><td>".$nomer."</td><td>".$data['nama']."</td><td>".$data['nim']."</td><td>".$data['ttl']."</td><td>".$data['alamat']."</td>";
			echo "<td><a href='".$_SERVER['PHP_SELF']."?act=edit&nim=".$data['nim']."'>Edit</a></td>";
			echo "<td><a onClick=\"javascript: return confirm('Hapus data ".$data['nim']." - ".$data['nama']."');\" href='".$_SERVER['PHP_SELF'].'?act=hapus&nim='.$data['nim']."'>Hapus</a></td>";
			echo "<td><a href='".$_SERVER['PHP_SELF']."?act=view&nim=".$data['nim']."'>Detail</a></td>";
		//}
		echo "</tr><!-- sabri sangjaya -->";
		$nomer++;
	}
	echo "</table>";
if(!(isset($_GET['act'])&&$_GET['act']=="view"&&isset($_GET['nim']))){
	echo "<br/>Halaman : ";
	//link prev
	if(!isset($_GET['page'])||isset($_GET['page'])&&$_GET['page']==1){
		echo " &laquo; Prev";
	}
	else if(isset($_GET['page'])){
		if(count($_GET)==0) $link = $_SERVER['REQUEST_URI']."?page=".((INT)$_GET['page']-1);
		if(isset($_GET['page'])){
			$link= $_SERVER['REQUEST_URI'];
			$link = preg_replace('~page=[^&]*~',"page=".((INT)$_GET['page']-1),$link);
		}
		else if (count($_GET)>0) $link = $_SERVER['REQUEST_URI']."&page=".((INT)$_GET['page']-1);
		if(isset($_GET['page'])&&$_GET['page']>1){
		echo " <a href='".$link."'>&laquo; Prev</a>";
		}
	}//link nomor
	for($i=1;$i<=$jumlahhalaman;$i++){
		if(count($_GET)==0) $link = $_SERVER['REQUEST_URI']."?page=".$i;
		if(isset($_GET['page'])){
			$link= $_SERVER['REQUEST_URI'];
			$link = preg_replace('~page=[^&]*~',"page=".$i,$link);
		}
		else if (count($_GET)>0) $link = $_SERVER['REQUEST_URI']."&page=".$i;
		
		if(isset($_GET['page'])&&$i==$_GET['page']){
			echo " <span class='boldtext'>".$i."</span>";
			continue;
		}
		else echo " <a href='".$link."'>".$i."</a>";
	}//link next
	if(isset($_GET['page'])&&$_GET['page']!=$jumlahhalaman){
		if(count($_GET)==0)$link = $_SERVER['REQUEST_URI']."?page=".((INT)$_GET['page']+1);
		
		if(isset($_GET['page'])){
			$link= $_SERVER['REQUEST_URI'];
			$link = preg_replace('~page=[^&]*~',"page=".((INT)$_GET['page']+1),$link);
		}
		else if (count($_GET)>0) $link = $_SERVER['REQUEST_URI']."&page=".((INT)$_GET['page']+1);
		echo " <a href='".$link."'> Next &raquo;</a>";
	}
	else if (!isset($_GET['page'])&&$jumlahhalaman!=1){
		$link = $_SERVER['REQUEST_URI']."&page=2";
		echo " <a href='".$link."'> Next &raquo;</a>";
	}
	else echo " Next &raquo;";
}
}else echo "Data kosong";
}
//Akhir Fungsi
//Fungsi editor data untuk tambah dan edit
function editdata($con){
	$nim=isset($_GET['nim'])?$_GET['nim']:"0";
	$sql="SELECT * FROM mahasiswa WHERE nim like '$nim'";
	$query = mysqli_query($con,$sql);
	if($query!=null) $data = mysqli_fetch_array($query);
	?>
	<form action="<?php echo $_SERVER['REQUEST_URI'];?>" method="post">
		<table><tr>
			<td>NIM</td>
			<td><input type="text" name="nim" size=40 maxlength="12" value="<?php echo isset($data['nim'])?$data['nim']:'';
				echo '"';echo ($_GET['act']=='tambah'||($_GET['act']=='edit'&&mysqli_num_rows($query)==0)|| ($_GET['act']=='edit'&&(!isset($_GET['nim']))))?'':' readonly';echo " required/></td>";?>
		</tr><tr>
			<td>Nama</td>
			<td><input type="text" name="nama" size=40 value="<?php echo isset($data['nama'])?$data['nama']:'';?>" required/></td>
		</tr><tr>
			<td>Tanggal Lahir</td>
			<td><input type="text" name="ttl" size=40 value="<?php echo isset($data['ttl'])?$data['ttl']:'';?>" /></td>
		</tr><tr>
			<td>Alamat</td>
			<td><input type="text" name="alamat" size=40 value="<?php echo isset($data['alamat'])?$data['alamat']:'';?>"/></td>
			</tr><tr>
			<td></td>
			<td><input type="submit" name="kirim" value="Simpan"/>
				<input type="button" value="Kembali" onclick="window.location.href='<?php echo $_SERVER['PHP_SELF'].'?act=view';?>'"/></td>
		</tr></table>
			</form>
			<?php
			if (isset($_POST['kirim'])){
				$nim = $_POST['nim'];
				if(isset($_GET['act'])&&$_GET['act']=="edit"&&$_GET['nim']!=$nim){
					echo "<script>alert('Invalid');window.location.href = '".$_SERVER['PHP_SELF']."?act=view';</script>";
				}
				else{
					$sql="SELECT * FROM mahasiswa WHERE nim like '$nim'";
					$query = mysqli_query($con,$sql);
					if($query==null){
						$nama = $_POST['nama'];
						$ttl = $_POST['ttl'];
						$alamat = $_POST['alamat'];
						$sql = "INSERT INTO mahasiswa VALUES('$nim','$nama','$ttl','$alamat')";
						if ($res = mysqli_query($con,$sql)) {
							echo "<script>alert('Berhasil ditambahkan');window.location.href = '".$_SERVER['PHP_SELF']."?act=view';</script>";
						} else echo 'Gagal Menambah Data <br />';
					}
					else{
						$nama = $_POST['nama'];
						$ttl = $_POST['ttl'];
						$alamat = $_POST['alamat'];
						if($_GET['act']=="edit"&&isset($_GET['nim'])){
							$sql = "UPDATE mahasiswa SET nim='$nim',nama='$nama',ttl='$ttl',alamat='$alamat' WHERE nim like '$nim'";
							if ($res = mysqli_query($con,$sql)) {
								echo "<script>alert('Berhasil diupdate');window.location.href = self.location;</script>";
							} else echo 'Gagal Update Data <br />';
						}
						else if($_GET['act']=="tambah"||$_GET['act']=="edit"){
							$sql = "INSERT INTO mahasiswa VALUES('$nim','$nama','$ttl','$alamat')";
							if ($res = mysqli_query($con,$sql)) {
								echo "<script>alert('Berhasil ditambahkan');window.location.href = '".$_SERVER['PHP_SELF']."?act=view';</script>";
							} else echo 'Gagal Menambah Data <br />';
						}
					}
				}
				
			}
		}
//akhir fungsi
		?>