<?php

/* JonTestBundle::layout.twig.html */
class __TwigTemplate_6a4f7288859e67e178f9f52561a4c553 extends Twig_Template
{
    protected $parent;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->blocks = array(
            'content' => array($this, 'block_content'),
            'body' => array($this, 'block_body'),
        );
    }

    public function getParent(array $context)
    {
        if (null === $this->parent) {
            $this->parent = $this->env->loadTemplate("::base.twig.html");
        }

        return $this->parent;
    }

    public function display(array $context, array $blocks = array())
    {
        $context = array_merge($this->env->getGlobals(), $context);

        $this->getParent($context)->display($context, array_merge($this->blocks, $blocks));
    }

    // line 6
    public function block_content($context, array $blocks = array())
    {
    }

    // line 3
    public function block_body($context, array $blocks = array())
    {
        // line 4
        echo "    <h1>Hello Application</h1>

    ";
        // line 6
        $this->displayBlock('content', $context, $blocks);
    }

    public function getTemplateName()
    {
        return "JonTestBundle::layout.twig.html";
    }
}
