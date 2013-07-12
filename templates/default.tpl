{{ #include header }}
	<section>
		<h1>Changelog</h1>

		{{ #each version in changelog }}
			<h2>{{ version.title }} - {{ version.date }}</h2>
			<p>{{ version.description }}</p>
			<ul>
				{{ #each bullet in version.changes }}
					<li>{{ bullet. }}</li>
				{{ /each }}
			</ul>
		{{ /each }}
	</section>

	<section>
		{{ twitter }}
	</section>

{{ #include footer }}