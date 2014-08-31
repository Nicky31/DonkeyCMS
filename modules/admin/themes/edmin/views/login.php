<div class="module module-login span4 offset4">
	<?php echo '<form class="form-vertical" action="'. siteUrl('admin/login/login') .'" method="post">'; ?>
		<div class="module-head">
			<h3>Connexion</h3>
		</div>
		<div class="module-body">
			<div class="control-group">
				<div class="controls row-fluid">
					<input name="username" class="span12" type="text" id="inputEmail" placeholder="Nom de compte">
				</div>
			</div>
			<div class="control-group">
				<div class="controls row-fluid">
					<input name="password" class="span12" type="password" id="inputPassword" placeholder="Mot de passe">
				</div>
			</div>
		</div>
		<div class="module-foot">
			<div class="control-group">
				<div class="controls clearfix">
					<button type="submit" class="btn btn-primary pull-right">Login</button>
				</div>
			</div>
		</div>
	</form>
</div>