{{ #include header }}
	{{ #each entry in teasers }}
		<article>
			<header>
				<h1><a href="{{entry.link}}">{{ entry.title }}</a></h1>
				<p class="metadata">{{ entry.date }} - {{ entry.author }}</p>
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