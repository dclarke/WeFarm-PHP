<h1>New farmer registration</h1>
<?php echo Form::open('user/complete_registration') ?>

  <div class="field">
    <label for="farmer_name">Name</label>
    <?php echo Form::input('username','',array('id' => 'username')) ?>
  </div>
  <div class="field">
    <label for="farmer_email">Email</label>
    <?php echo Form::input('email','',array('id' => 'email')) ?>
  </div>
  <div class="field">
    <label for="farmer_password">Password</label>
    <?php echo Form::password('password','',array('id' => 'password')) ?>
  </div>
  <div class="field">
    <label for="farmer_farm">Farm</label>
    <?php echo Form::input('farm','',array('id' => 'farm')) ?>
  </div>
  <div class="field">
    <label for="farmer_produce">Produce</label>
    <?php echo Form::input('produce','',array('id' => 'produce')) ?>
  </div>
  <div class="field">
    <label for="farmer_produce_price">Produce price</label>
    <?php echo Form::input('price','',array('id' => 'price')) ?>
  </div>
  <div class="actions">
    <?php echo Form::submit('submit','Create Farmer') ?>
  </div>
</Form>

<a href="/wefarm_php/">Back</a>
