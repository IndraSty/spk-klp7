<?php
$update = (isset($_GET['action']) AND $_GET['action'] == 'update') ? true : false;
if ($update) {
	$sql = $connection->query("SELECT * FROM penilaian WHERE kd_penilaian='$_GET[key]'");
	$row = $sql->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
	$validasi = false; $err = false;
	if ($update) {
		$sql = "UPDATE penilaian SET kd_kriteria='$_POST[kd_kriteria]', keterangan='$_POST[keterangan]', bobot='$_POST[bobot]' WHERE kd_penilaian='$_GET[key]'";
	} else {
		$sql = "INSERT INTO penilaian VALUES (NULL, '$_POST[kode_std]', '$_POST[kd_kriteria]', '$_POST[keterangan]', '$_POST[bobot]')";
		$validasi = true;
	}

	if ($validasi) {
		$q = $connection->query("SELECT kd_penilaian FROM penilaian WHERE kode_std=$_POST[kode_std] AND kd_kriteria=$_POST[kd_kriteria] AND keterangan LIKE '%$_POST[keterangan]%' AND bobot=$_POST[bobot]");
		if ($q->num_rows) {
			echo alert("Penilaian sudah ada!", "?page=penilaian");
			$err = true;
		}
	}

  if (!$err AND $connection->query($sql)) {
    echo alert("Berhasil!", "?page=penilaian");
  } else {
		echo alert("Gagal!", "?page=penilaian");
  }
}

if (isset($_GET['action']) AND $_GET['action'] == 'delete') {
  $connection->query("DELETE FROM penilaian WHERE kd_penilaian='$_GET[key]'");
	echo alert("Berhasil!", "?page=penilaian");
}
?>
<div class="row">
	<div class="col-md-4">
	    <div class="panel panel-<?= ($update) ? "warning" : "info" ?>">
	        <div class="panel-heading"><h3 class="text-center"><?= ($update) ? "EDIT" : "TAMBAH" ?></h3></div>
	        <div class="panel-body">
	            <form action="<?=$_SERVER['REQUEST_URI']?>" method="POST">
									<div class="form-group">
	                  <label for="kode_std">Studio</label>
										<select class="form-control" name="kode_std" id="mahasiswa">
											<option>---</option>
											<?php $sql = $connection->query("SELECT * FROM mahasiswa") ?>
											<?php while ($data = $sql->fetch_assoc()): ?>
												<option value="<?=$data["kode_std"]?>" <?= (!$update) ?: (($row["kode_std"] != $data["kode_std"]) ?: 'selected="selected"') ?>><?=$data["nama"]?></option>
											<?php endwhile; ?>
										</select>
									</div>
									<div class="form-group">
	                  <label for="kd_kriteria">Kriteria</label>
										<select class="form-control" name="kd_kriteria" id="kriteria">
											<option>---</option>
											<?php $sql = $connection->query("SELECT * FROM kriteria") ?>
											<?php while ($data = $sql->fetch_assoc()): ?>
												<option value="<?=$data["kd_kriteria"]?>" <?= (!$update) ?: (($row["kd_kriteria"] != $data["kd_kriteria"]) ?: 'selected="selected"') ?>><?=$data["nama"]?></option>
											<?php endwhile; ?>
										</select>
									</div>
	                <div class="form-group">
	                    <label for="keterangan">Keterangan</label>
	                    <input type="text" name="keterangan" class="form-control" <?= (!$update) ?: 'value="'.$row["keterangan"].'"' ?>>
	                </div>
	                <div class="form-group">
	                    <label for="bobot">Bobot</label>
	                    <input type="text" name="bobot" class="form-control" <?= (!$update) ?: 'value="'.$row["bobot"].'"' ?>>
	                </div>
	                <button type="submit" class="btn btn-<?= ($update) ? "warning" : "info" ?> btn-block">Simpan</button>
	                <?php if ($update): ?>
										<a href="?page=penilaian" class="btn btn-info btn-block">Batal</a>
									<?php endif; ?>
	            </form>
	        </div>
	    </div>
	</div>
	<div class="col-md-8">
	    <div class="panel panel-info">
	        <div class="panel-heading"><h3 class="text-center">DAFTAR</h3></div>
	        <div class="panel-body">
	            <table class="table table-condensed">
	                <thead>
	                    <tr>
	                        <th>No</th>
	                        <th>Studio</th>
	                        <th>Kriteria</th>
	                        <th>Keterangan</th>
	                        <th>Bobot</th>
	                        <th></th>
	                    </tr>
	                </thead>
	                <tbody>
	                    <?php $no = 1; ?>
	                    <?php if ($query = $connection->query("SELECT a.kd_penilaian, c.nama AS nama_mahasiswa, b.nama AS nama_kriteria, a.keterangan, a.bobot FROM penilaian a JOIN kriteria b ON a.kd_kriteria=b.kd_kriteria JOIN mahasiswa c ON a.kode_std=c.kode_std")): ?>
	                        <?php while($row = $query->fetch_assoc()): ?>
	                        <tr>
	                            <td><?=$no++?></td>
	                            <td><?=$row['nama_mahasiswa']?></td>
	                            <td><?=$row['nama_kriteria']?></td>
	                            <td><?=$row['keterangan']?></td>
	                            <td><?=$row['bobot']?></td>
	                            <td>
	                                <div class="btn-group">
	                                    <a href="?page=penilaian&action=update&key=<?=$row['kd_penilaian']?>" class="btn btn-warning btn-xs">Edit</a>
	                                    <a href="?page=penilaian&action=delete&key=<?=$row['kd_penilaian']?>" class="btn btn-danger btn-xs">Hapus</a>
	                                </div>
	                            </td>
	                        </tr>
	                        <?php endwhile ?>
	                    <?php endif ?>
	                </tbody>
	            </table>
	        </div>
	    </div>
	</div>
</div>

<script type="text/javascript">
$("#kriteria").chained("#beasiswa");
</script>
