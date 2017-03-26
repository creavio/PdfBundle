<?php

namespace Creavio\PdfBundle\Pdf;


use Symfony\Component\HttpFoundation\Response;

class PdfService
{
	/**
	 * @param array $options
	 * @return \mPDF
	 */
	public function getMpdf($options = [])
	{
		return new \mPDF('utf-8', 'A4');
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
			'mpdf' => null
		];
		$options = array_merge($defaultOptions, $options);

		if(!$options['mpdf']) {
			/** @var \mPDF */
			$options['mpdf'] = $this->getMpdf($options['constructorArgs']);
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