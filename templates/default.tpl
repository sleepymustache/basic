{{ #include header }}
	<h1>Changelog</h1>
	{{ #each change in changelog }}
		<h2>{{ change.title }}</h2>
		<p>{{ change.description }}</p>
	{{ /each }}
{{ #include footer }}