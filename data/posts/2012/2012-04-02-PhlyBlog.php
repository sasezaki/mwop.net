<?php
use Mwop\Blog\AuthorEntity;
use Mwop\Blog\EntryEntity;

$author = new AuthorEntity();
$author->setId('matthew');
$author->setName("Matthew Weier O'Phinney");
$author->setEmail("me@mwop.net");
$author->setUrl("http://mwop.net");

$entry = new EntryEntity();

$entry->setId('2012-05-developing-a-zf2-blog');
$entry->setTitle('Developing A ZF2 Blog');
$entry->setAuthor($author);
$entry->setDraft(false);
$entry->setPublic(true);
$entry->setCreated(new \DateTime('2012-04-03 08:45', new \DateTimezone('America/Chicago')));
$entry->setUpdated(new \DateTime('2012-04-03 21:50', new \DateTimezone('America/Chicago')));
$entry->setTimezone('America/Chicago');
$entry->setTags(array (
  0 => 'php',
  1 => 'zend framework',
  2 => 'zf2',
));

$body =<<<'EOT'
<p>
    This post tells a story.
</p>

<p>
    A long time ago, I set out to write my own blog platform. Yes, <a 
    href="http://wordpress.org">WordPress</a> is a fine blogging platform, as is 
    <a href="http://www.s9y.org/">Serendipity</a> (aka "s9y", and my previous 
    platform). And yes, I know about <a 
    href="http://habariproject.org">Habari</a>. And, for those of you skimming 
    ahead, yes, I'm quite aware of <a href="http://github.com/mojombo/jekyll">Jekyll</a>, 
    thank you anyways.
</p>

<p>
    Why write something of my own? Well, of course, there's the fact that I'm
    a developer, and have control issues. Then there's also the fact that a 
    blog is both a simple enough domain to allow easily experimenting with new
    technology and paradigms, while simultaneously providing a complex enough
    domain to expose non-trivial issues. 
</p>
    
<p>
    When I started this project, it was a technology-centered endeavor; I 
    wanted to play with document databases such as <a 
    href="http://couchdb.apache.org/">CouchDB</a> and <a 
    href="http://www.mongodb.org/">MongoDB</a>, and with caching technologies 
    like <a href="http://memcached.org">memcached</a> and <a 
    href="http://redis.io">redis</a>.
</p>

<p>
    Not long after I started, I also realized it was a great playground for me
    to prototype ideas for <a href="http://framework.zend.com/zf2">ZF2</a>;
    in fact, the original DI and MVC prototypes lived as branches of my blog.
    (My repository is still named "zf2sandbox" to this day, though it 
    technically houses just my site.)
</p>

<p>
    Over time, I had a few realizations. First, my <em>actual</em> blog was
    suffering. I wasn't taking the time to perform security updates, nor even
    normal upgrades, and was so far behind as to make the process non-trivial,
    particularly as I had a custom theme, and because I was proxying to my 
    blog via a ZF app in order to facilitate a cohesive site look-and-feel. I
    needed to either sink time into upgrading, or finish my blog.
</p>
    
<p>
    My second realization, however, was the more important one: I wanted a 
    platform where I could write how <em>I</em> want to write. I am
    a keyboard-centric developer and computer user, and while I love the web,
    I hate typing in its forms. Additionally, my posts often take longer than
    a typical browser session -- which leaves me either losing my work in a
    GUI admin, or having to write first in my editor of choice, and then 
    cut-and-paste it to the web forms. Finally, I want versions I can easily
    browse with standard diffing tools.
</p>

<p>
    When it came down to it, my blog content is basically static. Occasionally,
    I'll update a post, but it's rare. Comments are really the only dynamic
    aspect of the blog... and what I had with s9y was not cutting it, as I was
    getting more spam than I could keep up with. New commenting platforms such 
    as <a href="http://livefyre.com">Livefyre</a> and <a href="http://disqus.com">Disqus</a>
    provide more features than most blogging platforms I know, and provide 
    another side benefit: because they are javascript-based, you can simply
    drop in a small amount of markup into your post <em>once</em> -- meaning
    your pages can be fully static!
</p>

<p>
    Add these thoughts to the rise of static blogging platforms such as the 
    aforementioned Jekyll, and I had a kernel of an idea: take the work I'd
    done already, and create a static blog generator. 
</p>
EOT;
$entry->setBody($body);

$extended=<<<'EOT'
<h2>Getting There</h2>

<p>
    This was not an overnight decision, nor an all-nighter project.
</p>

<p>
    The ground-work for my blog goes back a couple years, and originates with
    some of my presentations on domain models. The "kernel" of my blog today
    is the same as when I began a couple years ago, and is simply a domain
    entity class in plain old PHP that composes some values and behaviors.
    It composes an InputFilter, which allows it to validate itself. I 
    have <code class="hl">fromArray()</code> and <code class="hl">toArray()</code> methods to allow
    easy de/serialization for both PHP and JSON. In a nutshell, though, the
    following properties define my entries:
</p>

<div class="example"><pre><code lang="php">
class EntryEntity implements EntityDefinition
{
    protected $filter;

    protected $id;
    protected $title;
    protected $body = '';
    protected $extended = '';
    protected $author;
    protected $isDraft = true;
    protected $isPublic = true;
    protected $created;
    protected $updated;
    protected $timezone = 'America/New_York';
    protected $tags = array();
    protected $metadata = array();
    protected $comments = array();
    protected $version = 2;
}
</code></pre></div>

<p>
    The "id" is the "stub", the unique identifier for a given
    post, and is used in URLs. "isDraft" and "isPublic" are used to determine 
    whether or not a post is published on the site, and exposed in the various
    paginated views. The "created" and "updated" fields are timestamps, 
    resolved in relation to the "timezone". Tags are incorporated directly as a 
    PHP array.
</p>

<p>
    Some fields are legacy fields. The "version" was originally for me to 
    transition entries -- those in a version "1" would fold in "comments" 
    directly in the entry (as an array of Comment entities), while those in
    version "2" would use Disqus. Once I discovered that I could import
    old comments into Disqus, I no longer needed to incorporate comments
    at all, nor have separate versions. "metadata" was information that
    s9y stored about the post -- usually hints for rendering, or information 
    on categories (which became simply tags in my system). In future versions,
    I'll likely remove these fields.
</p>

<p>
    I eventually added an "Author" entity. I realized I would like primarily to 
    re-use this code in places where multiple authors might occur (such as the 
    <a href="http://framework.zend.com/zf2/blog">ZF2 blog</a>).
    Additionally, feeds need authorship information, and while I could provide
    defaults, it's even better to incorporate it directly into the posts.
    It is a simple value-object, composing only an identifier, name, email, and 
    URL for the author.
</p>

<p>
    With these building blocks, I was able to do any number of things. I wrote a 
    script to grab my s9y entries from the database, create entry
    entities, and then push them into first CouchDB, and later MongoDB.  I 
    exported comments to WXR, and imported them to Disqus. The architecture 
    proved more than flexible enough for me to do all this. I wrote a service
    layer on top of my code to provide easy ways to get paginated lists of
    recent entries, entries by year, entries by tag, and more. I wrote a
    small, custom mapper to persist the data, as well as retrieve it. I wrote my 
    MVC layer to be restful, which would allow me to write entities locally, 
    and then serialize them to JSON and PUT them to the server. A simple 
    API-key authentication layer ensured I'd not get inundated with bogus, 
    third-party posts.
</p>

<p>
    Life was good, and I re-launched my blog in December 2011, using MongoDB.
</p>

<h2>Refactoring</h2>

<p>
    I had a RESTful API, and it worked. But it still didn't feel right. I have
    a small VPS, and running both Apache and MongoDB was starting to tax it. I
    tried an experiment with caching, but quickly discovered that for the amounts
    of data I wanted to cache, I'd use almost the same I/O as if I just served
    the content from my framework.
</p>

<p>
    And that RESTful API? Well, it worked, but it never felt like a natural way
    to post entries, never mind updates.
</p>

<p>
    I revisited the idea of a static blog, almost by accident. I started 
    wondering if I could pre-compile my blog views to prime my cache. And once
    I started, I realized I could actually just generate the entire blog
    as static files. And it was fast! (I can generate several hundred posts in
    a matter of seconds.)
</p>

<p>
    I exported all my entries from MongoDB to plain old PHP files (for those keeping track: fourth
    persistence format: MySQL, CouchDB, MongoDB, filesystem) that defined
    and returned entry entity objects. I then started writing a compiler and
    code generator in three parts. The first part iterates recursively over a 
    directory, looking for PHP files (in fact, I had <a 
        href="http://mwop.net/blog/244-Applying-FilterIterator-to-Directory-Iteration.html">to 
        use my blog to remember how to do this</a>). The second part iterates
    over those files, grabbing the entries, and putting them into PriorityQueue
    objects, based on their timestamp; newer entries sort to the top. The
    third part is a collection of methods that iterate over the various queues,
    rendering paginated views, tag clouds, feeds, and entries, and writing the
    artifacts to disk. (This last part actually leverages and bastardizes the
    new ZF2 View layer introduced in beta3.)
</p>

<p>
    The result is a ZF2 module I called <a href="http://git.mwop.net/?a=summary&p=PhlyBlog">PhlyBlog</a>.
    It needs some refinement still, but it works; you can generate all the
    above artifacts, and more.
</p>

<p>
    The interesting part about code generation is that you can generate just
    about anything you want. I started out thinking I'd generate some view
    scripts that my controller would consume and inject into the layout. But
    I realized quickly that I could just as easily inject them into the layout
    during the generation process.
</p>

<p>
    And this is where I realized how happy I was with where ZF2 has gone. I 
    removed my "blog" module from my repository, exported it into a standalone 
    repository, and re-imported it to my site as a "third-party" vendor module. 
    Now, the mantra with ZF2 developers is that you should not alter vendor
    code. However, if you do things right, you should be able to <em>override</em>
    configuration and dependencies via <em>local configuration</em>. So I did 
    just that, to see if it would work.
</p>

<p>
    I first created callbacks to pass to my configuration -- one to handle the
    tag cloud, and store it in a variable so I could inject it into templates
    later; the other was to set my rendering strategy, as well as create a 
    "response" strategy that would inject the blog contents into a layout.
</p>

<div class="example"><pre><code lang="php">
namespace Application;

use Traversable;
use Zend\View\Model;

class Module
{
    // there's more; this is just the blog-specific stuff

    protected static $layout;
    public static function prepareCompilerView($view, $config, $locator)
    {
        // Setup the rendering strategy for the blog; use the PhpRenderer
        $renderer = $locator->get('Zend\View\Renderer\PhpRenderer');
        $view->addRenderingStrategy(function($e) use ($renderer) {
            return $renderer;
        }, 100);

        // Setup a layout view model
        self::$layout = $layout   = new Model\ViewModel();
        $layout->setTemplate('layout');

        // Add a "response" strategy; inject the rendered blog contents
        // into the layout. When done, reset artifacts such as the title,
        // shortcut icon, and javascript.
        $view->addResponseStrategy(function($e) use ($layout, $renderer) {
            $result = $e->getResult();
            $layout->setVariable('content', $result);
            $page   = $renderer->render($layout);
            $e->setResult($page);

            // Cleanup
            $headTitle = $renderer->plugin('headtitle');
            $headTitle->getContainer()->exchangeArray(array());
            $headTitle->append('phly, boy, phly');

            $headLink = $renderer->plugin('headLink');
            $headLink->getContainer()->exchangeArray(array());
            $headLink(array(
                'rel' => 'shortcut icon',
                'type' => 'image/vnd.microsoft.icon',
                'href' => '/images/Application/favicon.ico',
            ));

            $headScript = $renderer->plugin('headScript');
            $headScript->getContainer()->exchangeArray(array());
        }, 100);
    }

    public static function handleTagCloud($cloud, $view, $config, $locator)
    {
        // If we don't have a layout, don't do anything
        if (!self::$layout) {
            return;
        }

        // Add the rendered tag cloud to the footer
        self::$layout->setVariable('footer', sprintf(
            "&lt;h4>Tag Cloud&lt;/h4>\n&lt;div class=\"cloud\">\n%s&lt;/div>\n",
            $cloud->render()
        ));
    }
}
</code></pre></div>

<p>
    I then overrode the configuration locally -- again, not in the module, but
    at the application level configuration. Specifically, I setup custom
    paths for where to write my files, custom link specifications, defaults
    for titles and author, and configuration for the tag cloud. I did something
    else, too: I told it to use specific templates, not the ones in the 
    official module I'd created. Finally, I told it to use the callbacks
    above.
</p>

<p>
    And it just worked.
</p>

<p>
    I've moved all of my modules into separate projects at this time, and built
    my site from these building blocks. The site itself is very lean, and just
    consists of some basic wiring to setup template names and paths, configuration
    for services I use, specifications for assets. Deployment is basically
    pushing files to my repository, and running a simple script to update my
    site version and configuration; it's completely hands-off now, a goal
    I've wanted for literally years.
</p>

<p>
    And you know what? I'm enjoying tweaking my site and making it my own again
    for the first time in years.
</p>

<h2>My Blog Process</h2>

<p>
    Blogging for me now is done in the following steps:
</p>

<ul>
    <li>I go to my local working directory for my site.</li>
    <li>I create a new branch off my development branch: <code class="hl">git checkout -b post/blahblah develop</code></li>
    <li>I start editing a "post" file: <code class="hl">vim content/some-new-post.php</code></li>
    <li>It's a PHP file that creates an EntryEntity. I fill in some details, and start writing
        the content in plain old HTML. If I want, I can use Markdown or some other format,
        and use a PHP library to convert it to HTML.</li>
    <li>I commit the code, edit, commit again, lather, rinse, repeat until I like it.</li>
    <li>I merge it back to my development branch, and generate my blog. (I have a CLI tool for this: <code class="hl">./console 
        PhlyBlog:compile</code>)</li>
    <li>I preview it locally, as a sanity check.</li>
    <li>I merge it to my master branch, and deploy.</li>
</ul>

<p>
    Yes, it's less "simple" than going to an admin page in the browser and 
    typing. But it allows me to craft my entries exactly how I want. I can have
    many entries being drafted at any given time, without needing to worry about
    session timeouts or accidently hitting a "publish" button. I can write my
    posts in the editor I want, and eliminate copy-and-paste entirely from my
    workflow. I don't have to worry about traffic bringing down my database 
    and/or server. In short, it's a good fit <em>for me</em>.
</p>

<h2>An invitation</h2>

<p>
    If you're interested in this story, I'll be talking about it, and showing
    how to develop both modules and sites built on top of modules this June, 
    via a full-day tutorial at the <a href="http://phpconference.nl">Dutch PHP Conference</a>.
    Please join me, and a host of incredible speakers, in Amsterdam this summer!
</p>

EOT;
$entry->setExtended($extended);

return $entry;
