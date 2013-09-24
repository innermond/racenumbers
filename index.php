<!DOCTYPE html>
<html>
  <head>
    <title>Bootstrap 101 Template</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="css/style.css" rel="stylesheet" media="screen">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="../../assets/js/html5shiv.js"></script>
      <script src="../../assets/js/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>

<div class="container">
  <div class="navbar-fixed-top">
  <div class="page-header custom-page-header">
    <h1>Race number generator <small>pdf producer</small></h1>
  </div>
  </div>

    <form id="race-number-form" role="form">
      <legend>Sheet</legend>
      <div class="row">
        <div class="form-group col-md-3 col-xs-12">
          <label for="sheet-width">Sheet Width</label>
          <div class="input-group">
            <input type="text" value="220" disabled class="form-control" id="sheet-width" placeholder="Width">
            <span class="input-group-addon">mm</span>
          </div>
        </div>

        <div class="form-group col-md-3 col-sm-12">
          <label for="sheet-height">Sheet Height</label>
          <div class="input-group">
            <select class="form-control" id="sheet-height">
              <option>150</option>
              <option>210</option>
            </select>
            <span class="input-group-addon">mm</span>
          </div>
        </div>
      </div>
      <div>
      <legend>Bleed & Crop Marks</legend>      
      <div class="btn-group" data-toggle="buttons">                            
        <label class="btn btn-default btn-sm">
          <input id="bleed-no" name="options" type="radio">No Bleed
        </label>
        <label class="btn btn-default btn-sm">
          <input id="bleed-yes" name="options" type="radio">10mm Bleed
        </label>
        <label class="btn btn-default btn-sm">
          <input id="bleed-yes-crop-marks" name="options" type="radio">10mm Bleed and Crop Marks
        </label>
      </div>
      </div>
      <div>
      <legend>Background Image</legend> 
      <div class="form-group">
        <label for="background-image">Upload background Image</label>
        <input type="file" id="background-image">
        <p id="background-image-help-block" class="help-block">Background image must be <span>220 mm x 150 mm</span> at 150 dpi.</p>
      </div>
      </div>
      <div>
      <legend>Race Numbers</legend>
      <ul class="nav nav-tabs" id="number-tab">
        <li><a href="#single">Single Number</a></li>
        <li><a href="#range">Number range</a></li>
      </ul>

      <div class="tab-content">
        <div class="tab-pane" id="single">
          <div class="form-group">
            <label for="number-single">Enter Number</label>
            <div class="input-group">
              <input type="number" value="1" class="form-control" id="number-single" placeholder="Number">
            </div>
          </div>
        </div>
        <div class="tab-pane" id="range">
          <div class="form-group col-md-3 col-sm-12">
            <label for="number-from">From</label>
            <div class="input-group">
              <input type="number" value="1" class="form-control" id="number-from" placeholder="From">
            </div>
          </div>
          <div class="form-group col-md-3 col-sm-12">
            <label for="number-to">To</label>
            <div class="input-group">
              <input type="number" value="1" class="form-control" id="number-to" placeholder="To">
            </div>
          </div>
          <div class="col-md-12 clear">
          <p class="help-block">You are creating <span>100</span> numbers.</p>
        </div>
        </div>
      </div>
      </div>
      <legend>Art Zone</legend>      
      <div class="row">
      <div class="form-group col-md-3 col-sm-12">
        <label for="font">Font</label>
        <div class="input-group">
          <select class="form-control" id="font">
            <option value="helvetica">Helvetica</option>
            <option value="calibri">Calibri</option>
            <option value="times">Timen New Roman</option>
          </select>
        </div>
      </div>

      <div class="form-group col-md-3 col-sm-12">
        <label for="position">Position</label>
        <div class="input-group">
          <select class="form-control" id="position">
            <option value="top">Top</option>
            <option value="center">Center</option>
            <option value="bottom">Bottom</option>
          </select>
        </div>
      </div>

      </div>

      <div class="col-12 clear spaced-bottom">
        <img id="img-preview" src="./img/preview.png" alt="Preview PDF" class="img-thumbnail">
      </div>

      <button type="button" class="btn btn-default">Preview</button>

      <div>
      <legend>Action</legend>      
        <div class="form-group">
          <button id="generate-pdf" type="button" class="btn btn-default">Generate PDF</button>
          <button id="save-project" type="button" class="btn btn-default">Save Project</button>
        </div>
      </div>

    </form>
  </div>
</div>

  <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
  <script src="//code.jquery.com/jquery.js"></script>
  <!-- Include all compiled plugins (below), or include individual files as needed -->
  <script src="js/bootstrap.min.js"></script>

  <script>
    $(function () {
      $('#number-tab a').click(function (e) {
        e.preventDefault()
        $(this).tab('show')
      })

      $('#number-tab a:first').tab('show');

      // $('.btn-group').button()


      var
      bleed = 10, 
      help_block = $('#background-image-help-block'),
      help_block_template = 'Background image must be <span class="badge">:W mm x :H mm</span> at <span class="badge">150 dpi</span>.',
      update_help_block = function(bleed) {
        if (typeof bleed == 'undefined') bleed = 0;
        help_block.html(help_block_template.replace(':W', 2*bleed + 1*$('#sheet-width').val()).replace(':H', 2*bleed + 1*$('#sheet-height').val()));
      }
      $('#sheet-height').on('click', function(){update_help_block();})
      $('#bleed-yes, #bleed-yes-crop-marks').on('change', function(event){
        if($(event.target).prop('checked')) update_help_block(bleed);
      ;
      })
      $('#bleed-no').on('change', function(event){ 
        if($(event.target).prop('checked')) update_help_block(0);
      })
       $('#sheet-height').click();
    })
  </script>

</body>
</html>