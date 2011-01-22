<?php

/* FrameworkBundle:Exception:exception_full.twig.html */
class __TwigTemplate_67128419ce9f66bb0c756cd9b3066bd2 extends Twig_Template
{
    protected $parent;

    public function __construct(Twig_Environment $env)
    {
        parent::__construct($env);

        $this->blocks = array(
            'body' => array($this, 'block_body'),
        );
    }

    public function getParent(array $context)
    {
        if (null === $this->parent) {
            $this->parent = $this->env->loadTemplate("FrameworkBundle:Exception:layout.twig.html");
        }

        return $this->parent;
    }

    public function display(array $context, array $blocks = array())
    {
        $context = array_merge($this->env->getGlobals(), $context);

        $this->getParent($context)->display($context, array_merge($this->blocks, $blocks));
    }

    // line 3
    public function block_body($context, array $blocks = array())
    {
        // line 4
        echo "    ";
        $template = "FrameworkBundle:Exception:exception.twig.html";
        if ($template instanceof Twig_Template) {
            $template->display($context);
        } else {
            echo $this->env->getExtension('templating')->getTemplating()->render($template, $context);
        }
    }

    public function getTemplateName()
    {
        return "FrameworkBundle:Exception:exception_full.twig.html";
    }
}
