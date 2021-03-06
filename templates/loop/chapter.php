<div class="cm-loop-chapter">
	<div class="cm-lchapter-inner">
		<div class="chapter-elements">
			<div class="e-chapter cm-chapter-thumbnail">
				<a href="<?php the_permalink(); ?>">
					<?php cmn_post_thumbnail( 'medium', array(), $comic->ID ); ?>
				</a>
			</div>
			<div class="e-chapter cm-chapter-name">
				<a href="<?php the_permalink(); ?>">
					<?php the_title(); ?>
				</a>
			</div>
			<div class="e-chapter right cm-likes">
				<span class="fa fa-thumbs-up"></span>
				<?php cominovel_echo( $chapter->likes ); ?>
			</div>
			<div class="e-chapter right cm-update-date">
				<?php cominovel_echo( $chapter->created_date() ); ?>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
</div>
