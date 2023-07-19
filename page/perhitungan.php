<div class="row">
	<div class="col-md-12">
	<?php if (isset($_GET["beasiswa"])) {
		$sqlKriteria = "";
		$namaKriteria = [];
		$queryKriteria = $connection->query("SELECT a.kd_kriteria, a.nama FROM kriteria a JOIN model b USING(kd_kriteria) WHERE b.kd_beasiswa=$_GET[beasiswa]");
		while ($kr = $queryKriteria->fetch_assoc()) {
			$sqlKriteria .= "SUM(
				IF(
					c.kd_kriteria=".$kr["kd_kriteria"].",
					IF(c.sifat='max', nilai.nilai/c.normalization, c.normalization/nilai.nilai), 0
				)
			) AS ".strtolower(str_replace(" ", "_", $kr["nama"])).",";
			$namaKriteria[] = strtolower(str_replace(" ", "_", $kr["nama"]));
		}
		$sql = "SELECT
			(SELECT nama FROM mahasiswa WHERE kode_std=mhs.kode_std) AS nama,
			(SELECT kode FROM mahasiswa WHERE kode_std=mhs.kode_std) AS kode,
			(SELECT tahun_mengajukan FROM mahasiswa WHERE kode_std=mhs.kode_std) AS tahun,
			$sqlKriteria
			SUM(
				IF(
						c.sifat = 'max',
						nilai.nilai / c.normalization,
						c.normalization / nilai.nilai
				) * c.bobot
			) AS rangking
		FROM
			nilai
			JOIN mahasiswa mhs USING(kode_std)
			JOIN (
				SELECT
						nilai.kd_kriteria AS kd_kriteria,
						kriteria.sifat AS sifat,
						(
							SELECT bobot FROM model WHERE kd_kriteria=kriteria.kd_kriteria AND kd_beasiswa=beasiswa.kd_beasiswa
						) AS bobot,
						ROUND(
							IF(kriteria.sifat='max', MAX(nilai.nilai), MIN(nilai.nilai)), 1
						) AS normalization
					FROM nilai
					JOIN kriteria USING(kd_kriteria)
					JOIN beasiswa ON kriteria.kd_beasiswa=beasiswa.kd_beasiswa
					WHERE beasiswa.kd_beasiswa=$_GET[beasiswa]
				GROUP BY nilai.kd_kriteria
			) c USING(kd_kriteria)
		WHERE kd_beasiswa=$_GET[beasiswa]
		GROUP BY nilai.kode_std
		ORDER BY rangking DESC"; ?>
	  <div class="panel panel-info">
	      <div class="panel-heading"><h3 class="text-center"><h2 class="text-center">HASIL PERHITUNGAN</h2></h3></div>
	      <div class="panel-body">
	          <table class="table table-condensed table-hover">
	              <thead>
	                  <tr>
					  <th>Kode</th>
							<th>Nama Studio</th>
							<?php //$query = $connection->query("SELECT nama FROM kriteria WHERE kd_beasiswa=$_GET[beasiswa]"); while($row = $query->fetch_assoc()): ?>
								<!-- <th><?//=$row["nama"]?></th> -->
							<?php //endwhile ?>
							<th>Nilai</th>
	                  </tr>
	              </thead>
	              <tbody>
					<?php $query = $connection->query($sql); while($row = $query->fetch_assoc()): ?>
					<?php
					$rangking = number_format((float) $row["rangking"], 8, '.', '');
					$q = $connection->query("SELECT kode_std FROM hasil WHERE kode_std='$row[kode]' AND kd_beasiswa='$_GET[beasiswa]' AND tahun='$row[tahun]'");
					if (!$q->num_rows) {
					$connection->query("INSERT INTO hasil VALUES(NULL, '$_GET[beasiswa]', '$row[kode]', '".$rangking."', '$row[tahun]')");
					}
					?>
					<tr>
					<td><?=$row["kode"]?></td>
						<td><?=$row["nama"]?></td>
						<?php for($i=0; $i<count($namaKriteria); $i++): ?>
						<!-- <th><?//=number_format((float) $row[$namaKriteria[$i]], 8, '.', '');?></th> -->
						<?php endfor ?>
						<td><?=$rangking?></td>
					</tr>
					<?php endwhile;?>
	              </tbody>
	          </table>
	      </div>
	  </div>
	<?php } else { ?>
		<h1>Studio belum dipilih...</h1>
	<?php } ?>
	</div>
</div>
