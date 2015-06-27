<?php

	$_l = array();


	$_l = array(
		'1' => array(
			'greating' => 'Action - Remplacement chaudière',
			'jumbo' => 'N\'attendez pas une panne de votre chaudière pour la remplacer, les frais de fonctionnement d\'une ancienne installation sont très élevés! Une nouvelle installation permettrait de faire de belles économies, en bonne intelligence! ',
			'benefits_title' => 'Voulez-vous faire des économies en bonne intelligence?',
			'benefits_subtitle' => 'Une nouvelle installation de chauffage offre plein d\'avantages:',
			'benefits' => array(
					'Baisse considérable des coûts énergétiques',
					'Moins d\'émissions et beaucoup plus écologique pour l\'environnement',
					'Fonctionnement beaucoup moins bruyant',
					'Plus de confort avec une connexion internet',
				),
		),
		'2' => array(
			'greating' => 'Aktion - Kesselaustausch',
			'jumbo' => 'Warten Sie nicht bis Ihre Heizung den Geist aufgibt um sie zu ersetzen, denn die höheren Laufkosten tragen Sie! Die Kosten sind wesentlich teurer als eine neue Installation!',
			'benefits_title' => 'Intelligent sparen durch Kesselaustausch!',
			'benefits_subtitle' => 'Eine neue Heizung bietet viele Vorteile:',
			'benefits' => array(
					'Senkt Ihre Energiekosten durch effizientes Heizen',
					'Stößt weniger Schadstoffe aus und schont die Umwelt',
					'Arbeitet deutlich geräuscharmer und nimmt weniger Platz weg',
					'Zusatzkomfort durch Anbindung ans Internet',
				),
		)
	);

?>



<div class="row">
	<div class="col-md-12 visible-md-12">
		&nbsp;
	</div>
</div>
<div class="row">
	<div class="col-md-12 hidden-xs" style="height: 100px;">
		&nbsp;
	</div>
</div>

<div class="row">
	<div class="col-md-12">
		<div class="jumbotron">
		  <h1><?php echo $_l[$id_lang]['greating'] ?></h1>
		  <p><?php echo $_l[$id_lang]['jumbo'] ?></p>
		</div>
	</div>
</div>

<div class="row">
	<div class="col-md-3">
		<img src="<?php echo base_url() ?>assets/img/img-action.png" width="160">
	</div>
	<div class="col-md-9">
		<h4><?php echo $_l[$id_lang]['benefits_title'] ?></h4>
		<p><?php echo $_l[$id_lang]['benefits_subtitle'] ?></p>
		<ul>
			<?php 
				foreach ($_l[$id_lang]['benefits'] as $value): ?>
	    		<li><?php echo $value ?></li>
	    	<?php endforeach; ?>
		</ul>
	</div>
</div>

<div class="row">
	<div class="col-md-12 hidden-xs" style="height: 100px;">
		&nbsp;
	</div>
</div>
