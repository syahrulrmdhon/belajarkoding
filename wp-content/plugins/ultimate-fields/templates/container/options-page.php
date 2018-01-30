<div id="poststuff">
	<form method="post" action="" class="wrap uf-wrap uf-options">
		<h1><?php echo $title ?></h1>

		<?php include( $ultimatefields->themes->path( 'container/error-message' ) ); ?>

		<div class="postbox">
			<h2 class="hndle"><?php echo $title ?></h2>

			<div class="inside uf-options-inside">
				<div class="uf-fields">
					<?php
					if( $this->description ) {
						echo wpautop( $this->description );
					}

					if( $links ) {
						echo $links;
					}

					$this->display_fields();
					?>
				</div>
			</div>
		</div>

		<?php
		# Display the submit button.
		submit_button( __( 'Save', 'uf' ), 'primary' );

		# Display an ajax loader
		include( $ultimatefields->themes->path( 'container/ajax-loader' ) );

		# Display a nonce field to prevent mistakes
		$this->nonce();
		?>
	</form>
</div>
