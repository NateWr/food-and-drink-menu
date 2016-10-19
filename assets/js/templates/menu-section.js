<li class="fdm-customize-section">
	<div class="header">
		<h4 class="title">
			{{{ data.title }}}
		</h4>
		<a href="#" class="fdm-toggle-component-form"></a>
	</div>
	<div class="control">
		<div class="setting">
			<label>
				<span class="customize-control-title"><?php esc_html_e( $this->i18n['section_name'] ); ?></span>
				<input type="text" value="{{{ data.title }}}" data-fdm-setting-link="title">
			</label>
			<label>
				<span class="customize-control-title"><?php esc_html_e( $this->i18n['section_description'] ); ?></span>
				<textarea data-fdm-setting-link="description">{{{ data.description }}}</textarea>
			</label>
			<# if ( !data.collection.length ) { #>
				<div class="placeholder">
					<?php esc_html_e( $this->i18n['no_menu_items'] ); ?>
				</div>
			<# } #>
			<ul class="fdm-menu-item-list"></ul>
		</div>
	</div>
	<div class="footer">
		<a href="#" class="fdm-remove-menu-section"><?php esc_html_e( $this->i18n['remove'] ); ?></a>
	</div>
</li>
