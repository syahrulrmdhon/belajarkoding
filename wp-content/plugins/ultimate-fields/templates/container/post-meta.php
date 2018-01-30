<div class="uf-wrap uf-postmeta">
	<div class="uf-fields">
		<?php
		if( $this->description ) {
			echo wpautop( $this->description );
		}

		if( $tab_links ) {
			do_action( 'uf_before_tabs', $this );
			echo $tab_links;
			do_action( 'uf_after_tabs', $this );
		}

		$this->display_fields();
		$this->nonce();
		?>
	</div>
</div>
