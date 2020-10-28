{{ #include header }}
  {{ #each entry in teasers }}
    <article class="row">
      <header class="col-12">
        <h1><a href="{{entry.link}}">{{ entry.title }}</a></h1>
        <p class="metadata">{{ entry.date }} - {{ entry.author }}</p>
      </header>
      <img class="col-12 col-md-4" alt="misc" src="{{ entry.image }}" />
      <footer class="col-12 col-md-8">
        <p>{{ entry.description }}</p>
        <p>
          <strong>Tags: </strong>
          {{ #each t in entry.tags }}
            <a href="{{ t.link }}">{{ t.name }}</a>
          {{ /each }}
        </p>
      </footer>
    </article>
  {{ /each }}
{{ #include footer }}