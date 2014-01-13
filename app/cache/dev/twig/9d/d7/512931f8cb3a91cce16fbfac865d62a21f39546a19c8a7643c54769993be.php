<?php

/* ::base.html.twig */
class __TwigTemplate_9dd7512931f8cb3a91cce16fbfac865d62a21f39546a19c8a7643c54769993be extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = false;

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'head' => array($this, 'block_head'),
            'body' => array($this, 'block_body'),
        );
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        // line 1
        echo "<!DOCTYPE html>
<html>
    <head>
        <meta charset=\"UTF-8\" />
        <title>";
        // line 5
        $this->displayBlock('title', $context, $blocks);
        echo "</title>
        ";
        // line 6
        $this->displayBlock('head', $context, $blocks);
        // line 7
        echo "    </head>
    <body>
        ";
        // line 9
        $this->displayBlock('body', $context, $blocks);
        // line 10
        echo "    </body>
</html>
";
    }

    // line 5
    public function block_title($context, array $blocks = array())
    {
        echo "Twitter";
    }

    // line 6
    public function block_head($context, array $blocks = array())
    {
    }

    // line 9
    public function block_body($context, array $blocks = array())
    {
    }

    public function getTemplateName()
    {
        return "::base.html.twig";
    }

    public function getDebugInfo()
    {
        return array (  57 => 9,  46 => 5,  40 => 10,  32 => 6,  28 => 5,  22 => 1,  79 => 16,  74 => 13,  68 => 17,  66 => 16,  60 => 13,  56 => 11,  53 => 10,  47 => 7,  39 => 5,  34 => 7,  31 => 3,  71 => 23,  58 => 12,  52 => 6,  49 => 10,  43 => 6,  41 => 7,  38 => 9,  35 => 5,  29 => 3,);
    }
}
