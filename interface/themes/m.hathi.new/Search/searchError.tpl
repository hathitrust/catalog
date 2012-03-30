{literal}
<style type="text/css">
  #tips h1 {font-size: 150%; padding-bottom: .25em;}
  #tips h2 {margin-top: 1.5em; font-size: 125%}
  #tips h3 {margin-top: 1.5em; font-size: 100%}
  #tips p {margin-left: 2em;}
  #tips {padding: 1.5em; width: 80%;}
  #tips ul, #tips ul li {background-color: #fff;}
  #tips ul li {list-style-type: disc; margin-left: 3.5em;}
  #tips em {font-style: italic}
</style>

{/literal}

<div id="tips">
<h1>{$error}</h1>

The most common errors are a wildcard character (* or ?) with a space before it, or a query that ends in AND or OR.


{include file="searchtips.tpl"}



</div>