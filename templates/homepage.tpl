{{ #include header }}
	<h1>News</h1>
	{{ #each entry in teasers }}
		<article>
			<header>
				<h2><a href="{{entry.link}}">{{ entry.title }}</a></h2>
				<p>{{ entry.date }} - {{ entry.author }}</p>
			</header>
			<p>{{ entry.description }}</p>
			<p>
				<strong>Tags: </strong>
				{{ #each t in entry.tags }}
					<a href="{{ t.link }}">{{ t.name }}</a>
				{{ /each }}
			</p>
		</article>
	{{ /each }}
{{ #include footer }}