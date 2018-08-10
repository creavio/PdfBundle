<?php

namespace Creavio\PdfBundle\Pdf;


use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class PdfService
 * @package Creavio\PdfBundle\Pdf
 */
class PdfService
{
	/**
	 * @var ContainerInterface
	 */
	private $container;

	/**
	 * @param ContainerInterface $container
	 */
	public function __construct(ContainerInterface $container)
	{
		$this->container = $container;
	}


	/**
	 * @param string $mode
	 * @param string $format
	 * @param int $default_font_size
	 * @param string $default_font
	 * @param int $mgl
	 * @param int $mgr
	 * @param int $mgt
	 * @param int $mgb
	 * @param int $mgh
	 * @param int $mgf
	 * @param string $orientation
	 * @return \mPDF
	 */
	public function getMpdf($mode='', $format='A4', $default_font_size=0, $default_font='', $mgl=15, $mgr=15, $mgt=16, $mgb=16, $mgh=9, $mgf=9, $orientation='P')
	{
		return new \mPDF($mode, $format, $default_font_size, $default_font, $mgl, $mgr, $mgt, $mgb, $mgh, $mgf, $orientation);
	}

	/**
	 * @param \mPDF $mpdf
	 * @param $html
	 * @return mixed
	 */
	public function setHeader($mpdf, $html)
	{
		$mpdf->SetHTMLHeader($html, '', true);

		return $mpdf;
	}

	/**
	 * @param string $html
	 * @param array $options
	 * @return string
	 */
	public function generatePdf($html, array $options = [])
	{
		$defaultOptions = [
			'constructorArgs' => [],
			'htmlMode' => null,
			'htmlInitialise' => null,
			'htmlClose' => null,
			'filename' => '',
			'destination' => 'S',
			'mpdf' => null,
			'cssFile' => null
		];
		$options = array_merge($defaultOptions, $options);

		if (!$options['mpdf']) {
			/** @var \mPDF */
			$options['mpdf'] = $this->getMpdf($options['constructorArgs']);
		}

		if ($options['cssFile']) {
			$path = $this->container->get('kernel')->locateResource($options['cssFile']);
			$stylesheet = file_get_contents($path);
			$options['mpdf']->WriteHTML($stylesheet, 1);
		}

		$options['mpdf']->WriteHtml($html, $options['htmlMode'], $options['htmlInitialise'], $options['htmlClose']);

		//TODO implement css files
		return $options['mpdf']->Output($options['filename'], $options['destination']);
	}

	/**
	 * @param $html
	 * @param array $options
	 * @return Response
	 */
	public function generatePdfResponse($html, array $options = array())
	{
		$response = new Response();
		$response->headers->set('Content-Type', 'application/pdf');
		$response->setContent($this->generatePdf($html, $options));

		return $response;
	}
}