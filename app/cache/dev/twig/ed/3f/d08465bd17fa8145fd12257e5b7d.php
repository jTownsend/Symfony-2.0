<?php

/* JonTestBundle:Jon:index.twig.html */
class __TwigTemplate_ed3fd08465bd17fa8145fd12257e5b7d extends Twig_Template
{
    protected $parent;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->blocks = array(
            'content' => array($this, 'block_content'),
        );
    }

    public function getParent(array $context)
    {
        if (null === $this->parent) {
            $this->parent = $this->env->loadTemplate("JonTestBundle::layout.twig.html");
        }

        return $this->parent;
    }

    public function display(array $context, array $blocks = array())
    {
        $context = array_merge($this->env->getGlobals(), $context);

        $this->getParent($context)->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_content($context, array $blocks = array())
    {
        // line 4
        echo "    Hello ";
        echo twig_escape_filter($this->env, $this->getContext($context, 'name', '4'), "html");
        echo "!
";
    }

    public function getTemplateName()
    {
        return "JonTestBundle:Jon:index.twig.html";
    }
}
