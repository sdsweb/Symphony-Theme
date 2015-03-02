		<div class="clear"></div>

		<!-- Footer -->
		<footer id="footer">
			<?php if ( ! symphony_is_fixed_width() ) : ?>
				<div class="in">
			<?php endif; ?>
			<section class="footer-widgets-container cf">
				<section class="footer-widgets cf <?php echo ( is_active_sidebar( 'footer-sidebar' ) ) ? 'widgets' : 'no-widgets'; ?>">
					<?php sds_footer_sidebar(); // Footer (3 columns) ?>
				</section>
			</section>

			<section class="copyright-area <?php echo ( is_active_sidebar( 'copyright-area-sidebar' ) ) ? 'widgets' : 'no-widgets'; ?>">
				<?php sds_copyright_area_sidebar(); ?>
			</section>

			<section class="copyright">
				<p class="copyright-message">
					<?php sds_copyright( 'Symphony' ); ?>
				</p>
			</section>

			<?php if ( ! symphony_is_fixed_width() ) : ?>
				</div>
			<?php endif; ?>
		</footer>

		<div class="clear"></div>
	</div>
	<!-- .in from header -->

	<?php wp_footer(); ?>
	</body>
</html>