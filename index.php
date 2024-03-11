<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Cekresi RajaOngkir</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/all.min.css" integrity="sha512-q3eWabyZPc1XTCmF+8/LuE1ozpg5xxn7iO89yfSOd5/oKvyqLngoNGsx8jq92Y8eXJ/IRxQbEC+FGSYxtk2oiw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.9.0/css/brands.min.css" integrity="sha512-sKhd1NGM4i4pJj+3P+NVHisu2z5rKAwNG1IpWMdKsFWYlUHFSrsAO3geQ5QNKttkMPZNTo76tfg8jVx2ICP7qw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  </head>
  <body>
    <nav class="navbar bg-body-tertiary">
        <div class="container">
            <a class="navbar-brand">Cekresi RajaOngkir</a>
            <a href="https://github.com/renannazar" class="text-dark" target="_blank">
                <i class="fab fa-github"></i>
            </a>
        </div>
    </nav>

    <div class="row justify-content-center mt-5">
        <div class="col-md-5">
            <p class="bg-primary p-3 rounded text-white">
                <i class="fas fa-info"></i>
                Aplikasi untuk cekresi dengan menggunakan API RajaOngkir, anda perlu memiliki API tersebut terlebih dahulu agar bisa menggunakan aplikasi ini.
                Setting API ada di .env pada file program ini.
            </p>

            <form method="POST">
              <textarea class="form-control" rows="6" name="no_resi" placeholder="Masukkan nomor resi, pisahkan antara satu resi dengan koma"></textarea>
              <select name="kurir" class="form-control mt-2">
                <option value="jne" selected>JNE</option>
                <option value="jnt">JNT</option>
                <option value="pos">POS INDONESIA</option>
              </select>
              <div class="mt-2 text-end">
                <button name="submit" class="btn btn-success">Cek Resi</button>
              </div>
            </form>

            <?php

            $env = parse_ini_file('.env');
            $apiKey = $env['API_KEY'];

            if (isset($_POST['submit']) && isset($_POST['no_resi'])) {
              $dataResi = explode(',', $_POST['no_resi']);
              $kurir = $_POST['kurir'];

              $dataAll = [];
              foreach ($dataResi as $key => $value) {
                $curl = curl_init();
                $resi = trim($value);

                curl_setopt_array($curl, array(
                  CURLOPT_URL => "https://pro.rajaongkir.com/api/waybill",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 30,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "POST",
                  CURLOPT_POSTFIELDS => "waybill=$resi&courier=$kurir",
                  CURLOPT_HTTPHEADER => array(
                    "content-type: application/x-www-form-urlencoded",
                    "key: $apiKey"
                  ),
                ));

                $response = curl_exec($curl);
                $err = curl_error($curl);

                curl_close($curl);

                if ($err) {
                  echo "cURL Error #:" . $err;
                } else {
                  array_push($dataAll, $response);
                }
              }
              
            }

            ?>

            <?php
            if (!empty($dataAll)):
            ?>
            <div class="mt-3">
              <?php foreach($dataAll as $data): ?>
              <?php $dataJson = json_decode($data)->rajaongkir ?>
              <div class="card mb-4">
                <div class="card-header text-uppercase">
                  No Resi : <?= $dataJson->query->waybill ?>, <b>Kurir : <?= $dataJson->query->courier ?></b>
                </div>
                <div class="card-body">
                  <?php if(!empty($dataJson->result)):  ?>
                    Status Paket : <?= $dataJson->result->delivery_status->status ?> <br>
                    Penerima : <?= $dataJson->result->delivery_status->pod_receiver ?> <br>
                    Tanggal : <?= $dataJson->result->delivery_status->pod_date ?> | <?= $dataJson->result->delivery_status->pod_time ?> <br>
                  <?php else: ?>
                    <?= $dataJson->status->description ?>
                  <?php endif?>
                </div>
              </div>
              <?php endforeach ?>
            </div>
            <?php endif ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>