<?php

/* TwitterBundle::layout.html.twig */
class __TwigTemplate_6be0c7fe2489fc727b5013853c33cbb56510c318c653700c003a4af0763763d8 extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("::base.html.twig");

        $this->blocks = array(
            'head' => array($this, 'block_head'),
            'body' => array($this, 'block_body'),
            'global_nav' => array($this, 'block_global_nav'),
            'main' => array($this, 'block_main'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "::base.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_head($context, array $blocks = array())
    {
        // line 4
        echo "    <link rel=\"stylesheet\" href=\"";
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/twitter/css/main.css"), "html", null, true);
        echo "\" />
    <script src=\"";
        // line 5
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/twitter/js/jquery-1.10.2.js"), "html", null, true);
        echo "\"></script> 
    <script src=\"";
        // line 6
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/twitter/js/bootstrap.js"), "html", null, true);
        echo "\"></script> 
    <script src=\"";
        // line 7
        echo twig_escape_filter($this->env, $this->env->getExtension('assets')->getAssetUrl("bundles/twitter/js/main.js"), "html", null, true);
        echo "\"></script>
";
    }

    // line 10
    public function block_body($context, array $blocks = array())
    {
        // line 11
        echo "    <div class=\"wrapper\">
            <div class=\"global-nav\">
                ";
        // line 13
        $this->displayBlock('global_nav', $context, $blocks);
        echo "    
            </div>
            <div class=\"main\">
                ";
        // line 16
        $this->displayBlock('main', $context, $blocks);
        // line 17
        echo "            </div> 
        </div>
";
    }

    // line 13
    public function block_global_nav($context, array $blocks = array())
    {
    }

    // line 16
    public function block_main($context, array $blocks = array())
    {
    }

    public function getTemplateName()
    {
        return "TwitterBundle::layout.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  79 => 16,  74 => 13,  68 => 17,  66 => 16,  60 => 13,  56 => 11,  53 => 10,  47 => 7,  39 => 5,  34 => 4,  31 => 3,  71 => 23,  58 => 12,  52 => 11,  49 => 10,  43 => 6,  41 => 7,  38 => 6,  35 => 5,  29 => 3,);
    }
}
