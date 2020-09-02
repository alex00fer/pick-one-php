<?php 
  require './utils/config_ini.php';
  $conf = get_config_ini();
  
  require './utils/random_image.php';
  $imgs = get_two_random_images($conf['img_folder']);
  $img_a = $imgs[0];
  $img_b = $imgs[1];
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <title>PickOne Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>

  <style>
    body {
      background-color: rgb(37, 37, 37);
    }

    h1 {
      color: white;
      margin: 0;
    }

    .header {
      background-color: #66395c;
      height: 10vh;
    }

    .img-container {
      height: 90vh;
      background-color: rgb(37, 37, 37);
      cursor: pointer;
      box-shadow:inset 0px 0px 0px 0px white;
      transition: box-shadow 0.1s linear;
      background-size:     contain;                
      background-repeat:   no-repeat;
      background-position: center center;
    }

    .img-container:hover {
      box-shadow:inset 0px 0px 0px 4px white;
    }

    #img-a {
      background-image:    url(<?php echo $img_a ?>);
    }

    #img-b {
      background-image:    url(<?php echo $img_b ?>);
    }

  </style>

</head>

<body>

  <div class="container-fluid">
    <div class="row header align-items-center">
      <div class="col-12 text-center">
        <h1 class="">Pick one image</h1>
      </div>
    </div>

    <div class="row">
      <div id="img-a" onclick="picked('<?php echo $img_a ?>', '<?php echo $img_b ?>')" 
        class="col-12 col-md-6 img-container"></div>
      <div id="img-b" onclick="picked('<?php echo $img_b ?>', '<?php echo $img_a ?>')" 
        class="col-12 col-md-6 img-container"></div>
    </div>
  </div>

  <script>
    function picked (selected, rejected) {
      // To send the POST data lets create a form with JS
      var form, selectedInput, rejectedInput;
      // Start by creating a <form>
      form = document.createElement('form');
      form.action = 'submit.php';
      form.method = 'post';
      // Next create the <input> with its values
      selectedInput = document.createElement('input');
      selectedInput.type = 'hidden';
      selectedInput.name = 'selected';
      selectedInput.value = selected;
      rejectedInput = document.createElement('input');
      rejectedInput.type = 'hidden';
      rejectedInput.name = 'rejected';
      rejectedInput.value = rejected;
      // Now put everything together
      form.appendChild(selectedInput);
      form.appendChild(rejectedInput);
      document.body.appendChild(form);
      // And submit it
      form.submit();
    }
  </script>

</body>

</html>