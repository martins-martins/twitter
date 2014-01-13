<?php

/* TwitterBundle:Twitter:login.html.twig */
class __TwigTemplate_03e68c66be0ff6f29f0a8eb6363267c7f553cc031544bde693f5ddde58330c6e extends Twig_Template
{
    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->parent = $this->env->loadTemplate("TwitterBundle::layout.html.twig");

        $this->blocks = array(
            'title' => array($this, 'block_title'),
            'main' => array($this, 'block_main'),
        );
    }

    protected function doGetParent(array $context)
    {
        return "TwitterBundle::layout.html.twig";
    }

    protected function doDisplay(array $context, array $blocks = array())
    {
        $this->parent->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_title($context, array $blocks = array())
    {
        echo "Twitter - Login";
    }

    // line 5
    public function block_main($context, array $blocks = array())
    {
        // line 6
        echo "    <div class=\"login\">
        ";
        // line 7
        if ((isset($context["success_message"]) ? $context["success_message"] : $this->getContext($context, "success_message"))) {
            // line 8
            echo "              <div class=\"text-success\">";
            echo twig_escape_filter($this->env, (isset($context["success_message"]) ? $context["success_message"] : $this->getContext($context, "success_message")), "html", null, true);
            echo "</div>
        ";
        }
        // line 10
        echo "        ";
        if ((isset($context["fail_message"]) ? $context["fail_message"] : $this->getContext($context, "fail_message"))) {
            // line 11
            echo "              <div class=\"text-danger\">";
            echo twig_escape_filter($this->env, (isset($context["fail_message"]) ? $context["fail_message"] : $this->getContext($context, "fail_message")), "html", null, true);
            echo "</div>
        ";
        }
        // line 12
        echo " 
        <h3>Welcome to Twitter</h3>
        <form method=\"post\" action=\"\">
            <div class=\"form-group\">
                <input name=\"username\" class=\"form-control\" placeholder=\"Username\">
            </div>
            <div class=\"form-group\">
                <input name=\"password\" type=\"password\" class=\"form-control\" placeholder=\"Password\">
            </div> 
            <input name=\"submit\" type=\"submit\" class=\"btn btn-default\" value=\"Log in\">
        </form>
        <a href=\"";
        // line 23
        echo $this->env->getExtension('routing')->getPath("_signup");
        echo "\">Sign up</a>
    </div>
";
    }

    public function getTemplateName()
    {
        return "TwitterBundle:Twitter:login.html.twig";
    }

    public function isTraitable()
    {
        return false;
    }

    public function getDebugInfo()
    {
        return array (  71 => 23,  58 => 12,  52 => 11,  49 => 10,  43 => 8,  41 => 7,  38 => 6,  35 => 5,  29 => 3,);
    }
}
