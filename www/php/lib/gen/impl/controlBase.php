<?php

class ControlBase
{
	private $parent = null;
	private $children = [];

	public function &add(&$mChild)
	{	
		array_push($this->children, $mChild);
		$mChild->parent = &$this;
		return $mChild;
	}

	public function &inHTMLCtrl(...$mArgs)
	{
		return $this->add(new HTMLControl(...$mArgs));
	}

	public function &literal(...$mArgs)
	{
		$this->add(new Literal(...$mArgs));
		return $this;
	}

	public function &inDiv(...$mArgs)
	{
		return $this->inHTMLCtrl('div', ...$mArgs);
	}

	public function &inSpan(...$mArgs)
	{
		return $this->inHTMLCtrl('span', ...$mArgs);
	}

	public function &strong($mX)
	{
		$this->inHTMLCtrl('strong')->literal($mX);
		return $this;
	}

	public function &file($mX)
	{
		ob_start();
		require($mX);
		$this->literal(ob_get_clean());
		return $this;
	}

	public function &h($mHLevel, $mX)
	{
		$res = $this->inHTMLCtrl('h'.$mHLevel);
		$res->literal($mX);
		return $this;
	}

	public function &hr()
	{
		$this->literal('<hr/>');
		return $this;
	}

	public function &br()
	{
		$this->literal('<br/>');
		return $this;
	}

	public function &out()
	{
		return $this->parent;
	}

	public function &root()
	{
		if($this->parent == null) return $this;
		return $this->parent->root();
	}	

	public function &bsIcon($mIcon)
	{
		$this->inSpan(['class' => 'glyphicon glyphicon-'.$mIcon]);
		return $this;
	}

	public function &inBSLinkBtn($mID)
	{
		return $this->inHTMLCtrl('a', ['class' => 'btn btn-default', 'id' => $mID]);
	}

	public function &inBSLinkBtnCloseModal()
	{
		$res = $this->inBSLinkBtn('');
		$res->bsLinkBtnAddDismissModal();
		$res->bsIcon('remove');
		return $this;
	}

	public function &bsLinkBtnAddDismissModal()
	{
		$this->addAttribute('data-dismiss', 'modal');
		return $this;
	}	

	public function &inBSModal($mID)
	{
		return $this->inDiv(['class' => 'modal fade', 'id' => $mID])->inDiv(['class' => 'modal-dialog'])->inDiv(['class' => 'modal-content']);
	}	

	public function &inBSModalHeader($mTitle)
	{
		$res = $this->inDiv(['class' => 'modal-header']);

		$res->inHTMLCtrl('h4', ['class' => 'modal-title'])
			->literal($mTitle);

		$res->inHTMLCtrl('a', ['href' => '#', 'class' => 'close', 'data-dismiss' => 'modal', 'aria-label' => 'Close'])
			->inSpan(['aria-hidden' => 'true']);

		return $res;
	}

	public function &inBSModalBody()
	{
		return $this->inDiv(['class' => 'modal-body']);
	}

	public function &inBSModalFooter()
	{
		return $this->inDiv(['class' => 'modal-footer']);
	}

	public function &inBSBtnGroup($mClass)
	{
		return $this->inDiv(['class' => 'btn-group '.$mClass]);
	}

	public function &label($mFor, $mCaption)
	{
		$this->inHTMLCtrl('label', ['for' => $mFor])
			->literal($mCaption);

		return $this;
	}

	public function &inBSFormTextbox($mID, $mCaption)
	{
		$res = $this->inDiv(['class' => 'form-group']);
		$res->label($mID, $mCaption)
			->inHTMLCtrl('input', ['type' => 'text', 'class' => 'form-control', 'id' => $mID, 'placeholder' => $mCaption]);

		return $res;
	}

	public function &bsFormTextbox($mID, $mCaption)
	{
		$this->inBSFormTextbox($mID, $mCaption);
		return $this;
	}

	public function &bsFormTextarea($mID, $mCaption, $mRows)
	{
		$this->inDiv(['class' => 'form-group'])
			->label($mID, $mCaption)
			->inHTMLCtrl('textarea', ['rows' => $mRows, 'class' => 'form-control', 'id' => $mID, 'placeholder' => $mCaption]);

		return $this;
	}

	public function printRoot()
	{
		$this->root()->printAll();
	}

	public function forChildren($mFn)
	{
		foreach($this->children as &$x)
		{
			$mFn($x);
		}
	}

	public function forChildrenRecursive($mFn)
	{
		foreach($this->children as &$x)
		{
			$mFn($x);
			$x->forChildren($mFn);
		}
	}

	public function forParentRecursive($mFn)
	{
		$currParent = &$this->parent;
		while($currParent != null)
		{
			$mFn($currParent);
			$currParent = &$currParent->$parent;
		}	
	}

	protected function toHTML()
	{

	}

	protected function getChildrenHTML()
	{
		$res = '';
		$this->forChildren(function(&$mX) use (&$res){ $res .= $mX->toHTML(); });
		return $res;
	}

	public function printAll()
	{
		print($this->toHTML());
	}
}

class Container extends ControlBase
{
	protected function toHTML()
	{
		return $this->getChildrenHTML();
	}
}	

class Literal extends ControlBase
{
	protected $html;

	protected function __construct($mHTML) 
	{
		$this->html = $mHTML;
	}

	protected function toHTML() 
	{
		return $this->html;
	}
}

class HTMLControl extends ControlBase
{
	protected $html;
	protected $tag;
	protected $attributes = [];

	public function __construct($mTag, $mAttributes = []) 
	{
		$this->html = '';
		$this->tag = $mTag;
		$this->setAttributes($mAttributes);
	}

	public function setAttributes($mAttributes)
	{
		$this->attributes = $mAttributes;
		return $this;
	}	

	public function addAttribute($mK, $mV)
	{
		$this->attributes[$mK] = $mV;
		return $this;
	}

	protected function toHTML() 
	{
		$this->html = '<'.$this->tag;
		foreach($this->attributes as $k => $v)
		{
			$this->html .= ' '.$k.'="'.$v.'"';
		}
		$this->html .= '>';
		$this->html .= $this->getChildrenHTML();
		$this->html .= '</'.$this->tag.'>';

		return $this->html;
	}
}

class BSModal extends Container
{
	private $header;
	private $body;
	private $footer;
	private $footerBtns;

	public function __construct($mID, $mTitle)
	{
		$inner = $this->inBSModal($mID);

		$this->header = $inner->add(new Container());
		$this->body = $inner->add(new Container());
		$this->footer = $inner->add(new Container());

		$this->header = $this->header->inBSModalHeader($mTitle);
		$this->body = $this->body->inBSModalBody();
		$this->footer = $this->footer->inBSModalFooter();

		$btnGroup = $this->footer->inBSBtnGroup('pull-right');
		$this->footerBtns = $btnGroup->add(new Container());
		$btnGroup->inBSLinkBtnCloseModal();
	}

	public function &inHeader() 
	{
		return $this->header;
	}
	public function &inBody() 
	{
		return $this->body;
	}
	public function &inFooterBtns() 
	{
		return $this->footerBtns;
	}
};

?>