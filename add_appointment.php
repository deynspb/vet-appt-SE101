<?php
require_once('./config.php');
$schedule = $_GET['schedule'];
?>
<div class="container-fluid">
    <form action="" id="appointment-form">
        <input type="hidden" name="id" value="<?php echo isset($id) ? $id : '' ?>">
        <input type="hidden" name="schedule" value="<?php echo isset($schedule) ? $schedule : '' ?>">
        <dl>
            <dt class="text-muted">Appointment Schedule</dt>
            <dd class=" pl-3"><b><?= date("F d, Y",strtotime($schedule)) ?></b></dd>
        </dl>
        <hr>
        <div class="row">
            <div class="col-md-6">
                <fieldset>
                    <legend class="text-muted">Owner Information</legend>
                    <div class="form-group">
                        <label for="owner_name" class="control-label">Name</label>
                        <input type="text" name="owner_name" id="owner_name" class="form-control form-control-border" placeholder="John D Smith" value ="<?php echo isset($owner_name) ? $owner_name : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="contact" class="control-label">Contact #</label>
                        <input type="text" name="contact" id="contact" class="form-control form-control-border" placeholder="09xxxxxxxx" value ="<?php echo isset($contact) ? $contact : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email" class="control-label">Email</label>
                        <input type="email" name="email" id="email" class="form-control form-control-border" placeholder="jsmith@sample.com" value ="<?php echo isset($email) ? $email : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="address" class="control-label">Address</label>
                        <textarea type="email" name="address" id="address" class="form-control form-control-sm rounded-0" rows="3" placeholder="Lot 2 Block 23, Here Subd., Over There City, Anywhere, 2306" required><?php echo isset($address) ? $address : '' ?></textarea>
                    </div>
                </fieldset>
            </div>
            <div class="col-md-6">
                <fieldset>
                    <legend class="text-muted">Pet Information</legend>
                    <div class="form-group">
                        <label for="category_id" class="control-label">Pet Type</label>
                        <select name="category_id" id="category_id" class="form-control form-control-border select2">
                            <option value="" selected disabled></option>
                            <?php 
                            $categories = $conn->query("SELECT * FROM category_list where delete_flag = 0 ".(isset($category_id) && !empty($category_id) ? " or id = '{$category_id}'" : "")." order by name asc");
                            while($row = $categories->fetch_assoc()):
                            ?>
                            <option value="<?= $row['id'] ?>" <?= isset($category_id) && in_array($row['id'],explode(',', $category_id)) ? "selected" : "" ?> <?= $row['delete_flag'] == 1 ? "disabled" : "" ?>><?= ucwords($row['name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="breed" class="control-label">Breed</label>
                        <input type="text" name="breed" id="breed" class="form-control form-control-border" placeholder="Siberian Husky" value ="<?php echo isset($breed) ? $breed : '' ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="age" class="control-label">Age</label>
                        <input type="text" name="age" id="age" class="form-control form-control-border" placeholder="1 yr. old" value ="<?php echo isset($age) ? $age : '' ?>" required>
                    </div>
                </fieldset>
                <div class="form-group">
                    <label for="service_id" class="control-label">Service(s)</label>
                    <?php 
                        $services = $conn->query("SELECT * FROM service_list where delete_flag = 0 ".(isset($service_id) && !empty($service_id) ? " or id in ('{$service_id}')" : "")." order by name asc");
                        while($row = $services->fetch_assoc()){
                            unset($row['description']);
                            $service_arr[] = $row;
                        }
                        ?>
                    <select name="service_id[]" id="service_id" class="form-control form-control-border select2" multiple>
                    </select>
                </div>
            </div>
        </div>
    </form>
</div>
<script>
var service = $.parseJSON('<?= json_encode($service_arr) ?>') || {};

$(function(){
  $('#uni_modal').on('shown.bs.modal', function(){
    $('#category_id').select2({
      placeholder: "Please Select Pet Type here.",
      width: '100%',
      dropdownParent: $('#uni_modal')
    });
    $('#service_id').select2({
      placeholder: "Please Select Service(s) Here.",
      width: '100%',
      dropdownParent: $('#uni_modal')
    });
  });

  // Prevent typing numerical and special characters and limit length for category_id
  $('#category_id').on('input', function(e) {
    var sanitized = $(this).val().replace(/[^A-Za-z\s]/g, ''); // Remove numerical and special characters
    $(this).val(sanitized.slice(0, 10)); // Limit to 10 characters
  });

  // Prevent typing numerical and special characters and limit length for service_id
  $('#service_id').on('input', function(e) {
    var sanitized = $(this).val().replace(/[^A-Za-z\s]/g, ''); // Remove numerical and special characters
    $(this).val(sanitized.slice(0, 10)); // Limit to 10 characters
  });
});



  $('#category_id').change(function(){
    var id = $(this).val();
    $('#service_id').html('');
    $('#service_id').select2('destroy');
    Object.keys(service).map(function(k){
      if ($.inArray(id, service[k].category_ids.split(',')) > -1){
        var opt = $("<option>");
        opt.val(service[k].id);
        opt.text(service[k].name);
        $('#service_id').append(opt);
      }
    });
    $('#service_id').select2({
      placeholder: "Please Select Service(s) Here.",
      width: '100%',
      dropdownParent: $('#uni_modal')
    });
    $('#service_id').val('').trigger('change');
  });

  // Prevent typing invalid characters for Name and limit length
  $('#owner_name').on('input', function(e) {
    var sanitized = $(this).val().replace(/[^A-Za-z\s]/g, '');
    $(this).val(sanitized.slice(0, 20)); // Limit to 20 characters
  });

  // Prevent typing invalid characters for Contact and limit length
  $('#contact').on('input', function(e) {
    var sanitized = $(this).val().replace(/\D/g, ''); // Remove non-numeric characters
    $(this).val(sanitized.slice(0, 11)); // Limit to 11 characters
  });

  // Limit Email length
  $('#email').on('input', function(e) {
    $(this).val($(this).val().slice(0, 30)); // Limit to 30 characters
  });

  // Prevent typing invalid characters for Address and limit length
  $('#address').on('input', function(e) {
    var sanitized = $(this).val().replace(/[^A-Za-z0-9\s]/g, '');
    $(this).val(sanitized.slice(0, 30)); // Limit to 30 characters
  });

  // Prevent typing invalid characters for Breed and limit length
  $('#breed').on('input', function(e) {
    var sanitized = $(this).val().replace(/[^A-Za-z]/g, ''); // Remove numerical and special characters
    $(this).val(sanitized.slice(0, 10)); // Limit to 10 characters
  });

  // Prevent typing invalid characters for Age and limit length
  $('#age').on('input', function(e) {
    var sanitized = $(this).val().replace(/\D/g, ''); // Remove non-numeric characters
    $(this).val(sanitized.slice(0, 2)); // Limit to 2 characters
  });

  $('#uni_modal #appointment-form').submit(function(e){
    e.preventDefault();
    var _this = $(this);
    $('.pop-msg').remove();  // Remove previous messages

    var isValid = true; // Flag to track validation status
    var errorMessage = ""; // String to store error messages

    // Validate Name (assuming you have an input with id="owner_name")
    var name = $('#owner_name').val();
    if (name.trim() === "") {
      isValid = false;
      errorMessage += "Please enter your name.\n";
    }

    // Validate Contact (assuming you have an input with id="contact")
    var contact = $('#contact').val();
    if (contact.trim() === "") {
      isValid = false;
      errorMessage += "Please enter your contact number.\n";
    } else if (contact.length < 7 || contact.length > 11) {
      isValid = false;
      errorMessage += "Contact number must be 7-11 characters long.\n";
    }

    // Validate Email (assuming you have an input with id="email")
    var email = $('#email').val();
    if (email.trim() === "") {
      isValid = false;
      errorMessage += "Please enter your email address.\n";
    }

    // Validate Address (assuming you have a textarea with id="address")
    var address = $('#address').val();
    if (address.trim() === "") {
      isValid = false;
      errorMessage += "Please enter your address.\n";
    }

    // Validate Breed
    var breed = $('#breed').val();
    if (breed.trim() === "") {
      isValid = false;
      errorMessage += "Please enter the breed.\n";
    }

    // Validate Age
    var age = $('#age').val();
    if (age.trim() === "") {
      isValid = false;
      errorMessage += "Please enter the age.\n";
    }

    if (!isValid) {
      // Display error message if validation fails
      var el = $('<div>');
      el.addClass("pop-msg alert-danger");
      el.text(errorMessage);
      _this.prepend(el);
      el.show('slow');
      $('html,body,.modal').animate({scrollTop:0},'fast');
      return; // Prevent form submission
    }

    // If validation passes, proceed with form submission using AJAX
    $.ajax({
      url: _base_url_ + "classes/Master.php?f=save_appointment",
      data: _this.serialize(), // Serialize the form data
      method: 'POST',
      dataType: 'json',
      error: function(err) {
        console.log(err);
        alert_toast("An error occurred", 'error');
      },
      success: function(resp) {
        if (resp.status == 'success') {
          setTimeout(() => {
            uni_modal("Success", "success_msg.php?code=" + resp.code);
          }, 750);
        } else if (!!resp.msg) {
          var el = $('<div>');
          el.addClass("pop-msg alert-danger");
          el.text(resp.msg);
          _this.prepend(el);
          el.show('slow');
          $('html,body,.modal').animate({scrollTop:0},'fast');
        } else {
          var el = $('<div>');
          el.addClass("pop-msg alert-danger");
          el.text("An error occurred due to an unknown reason.");
          _this.prepend(el);
          el.show('slow');
          $('html,body,.modal').animate({scrollTop:0},'fast');
        }
      }
    });

  });


</script>