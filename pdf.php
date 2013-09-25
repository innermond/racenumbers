<?php

include __DIR__.'/../libs/tcpdf/tcpdf.php';

class RaceNumbers extends TCPDF {

	const BLEED = 10;

	protected
	$showCrop = false, 
	$data = array(),
	$_data = array(
		'width' => 210, 'height' => 150, 'bleed' => 0, 'cropmarkSize' => 5, 
		'font' => 'times', 'fontSize' => 275, 'position' => 'C',
		'range' => array(0,1), 'dpi' => 150, 'safe' => 5);

	public function __get($var) {

		switch ($var) 
		{
			case 'offset':
				return $this->bleed + $this->safe;
			break;
			case 'H_bleed':
				return $this->height + 2 * $this->bleed;
			break;
			case 'W_bleed':
				return $this->width + 2 * $this->bleed;
			break;
			case 'H_safe':
				return $this->height - 2 * $this->safe;
			break;
			case 'W_safe':
				return $this->width - 2 * $this->safe;
			break;
			default:
				if (isset($this->data[$var])) return $this->data[$var];
			break;
		}
		
		// throw new OutOfBoundsException;
	}

	public function init() {

		$this->SetCreator(PDF_CREATOR);
		$this->SetAuthor('Race Nation');
		$this->SetTitle('Starting');

		$this->SetMargins($this->offset, $this->offset, -1, true);
		$this->setPrintHeader(false);
		$this->setPrintFooter(false);
		$this->setCellMargins(0,0,0,0);
		$this->setCellPaddings(0,0,0,0);
		$this->SetAutoPageBreak(false, 0);
		$this->setPageUnit('mm');
		$this->setPageFormat(array($this->W_bleed, $this->H_bleed), $orientation='L');
		TCPDF_STATIC::setPageBoxes(1, 'BleedBox', $this->bleed, $this->height + $this->bleed, $this->width, $this->bleed, false, 1);
		// indicate corect proportion
		$this->SetImageScale($this->dpi/72);

		$this->setFontSubsetting(true);
	}

	public function canvas() {
		$color = array(0,0,0,0);
		$this->SetFillColorArray($color);
		$this->Rect(0,0, $this->W_bleed, $this->H_bleed, 'F');
	}

	public function cropmarks() {
		$this->cropMark($this->bleed, $this->bleed, $this->cropmarkSize, $this->cropmarkSize, 'TL');
		$this->cropMark($this->width + $this->bleed, $this->bleed, $this->cropmarkSize, $this->cropmarkSize, 'TR');
		$this->cropMark($this->bleed, $this->height + $this->bleed, $this->cropmarkSize, $this->cropmarkSize, 'BL');
		$this->cropMark($this->width + $this->bleed, $this->height + $this->bleed, $this->cropmarkSize, $this->cropmarkSize, 'BR');
	}

	public function draw_bleedborder() {
		$this->setVisibility('screen');
		// media
		$this->SetDrawColorArray(array(0, 0, 0, 70));
		$this->SetLineStyle(array('dash' => 1));
		$this->Rect(0, 0, $this->W_bleed, $this->H_bleed, 'D');
		// bleed
		$this->SetDrawColorArray(array(0, 100, 0, 0));
		$this->SetLineStyle(array('dash' => 1));
		$this->Rect($this->bleed, $this->bleed, $this->width, $this->height, 'D');
		// safe
		$this->SetDrawColorArray(array(100, 0, 0, 0));
		$this->SetLineStyle(array('dash' => 1));
		$this->Rect($this->offset, $this->offset, $this->width - 2*$this->safe, $this->height - 2*$this->safe, 'D');
		// $this->setVisibility('all');
	}

	public function draw_number($number = null) {
		// $this->setXY($this->offset, $this->offset);
		$w = $this->GetStringWidth($number, $fontname=$this->font, $fontstyle='B', $fontsize=$this->fontSize);
		$h = $this->getStringHeight(0, $number, $reseth=false, $autopadding=false, $cellpadding=0.0);
		$this->Cell(0, $this->height - 2*$this->safe, $number, 1, 0, 'C', true, '', 3, true, 'T', $this->position);
	}

	protected function allowed($data) {
		$default = $this->_data;
		return array_intersect_key($data, $default) + $default;
	}

	public function data($data) {
		// accept only data as declared in $this->data
		$this->data = $this->allowed($data);
		if ($this->data['bleed'] == 2) $this->showCrop = true;
		$this->data['bleed'] > 0 and $this->data['bleed'] = static::BLEED;

		list($a, $z) = $this->data['range'];
		$this->data['range'] = range($a, $z);

		return $this->data;
	}

	public function render() {

		try {
			
			$this->SetFont($this->font, 'B', $this->fontSize);


			$file = './pdf/'.uniqid();
			$this->init();
			foreach($this->range as $number) {
				$this->AddPage();
				$this->canvas();
				$this->draw_bleedborder();
				$this->showCrop and $this->cropmarks();

				$this->draw_number($number);
			}	
			$this->Output($file = $file.'.pdf', 'F');

			die($file);
		}

		catch (Exception $e){
			var_export($e);
		}
	}
}


$is_ajax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
if ($is_ajax) {
	$pdf = new RaceNumbers;
	$data = $pdf->data($_POST);
	$pdf->render();
}