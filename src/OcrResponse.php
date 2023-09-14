<?php

namespace JFuentesTgn\OcrSpace;

class OcrResponse
{

    protected $ocrResponseItem = null;
    protected $parsedResults = [];
    protected $ocrExitCode = null;
    protected $isErroredOnProcessing = false;
    protected $errorMessage = null;
    protected $errorDetails = null;
    protected $processingTime = null;
    protected $searchablePDFURL = '';

    public function __construct($jsonResponse)
    {
        $this->jsonResponse = $jsonResponse;
        $this->parseResponse();
    }

    protected function parseResponse()
    {
        $this->parsedResults = isset($this->jsonResponse->ParsedResults) ? $this->jsonResponse->ParsedResults : null;
        $this->ocrExitCode = isset($this->jsonResponse->OCRExitCode) ? $this->jsonResponse->OCRExitCode : null;
        $this->isErroredOnProcessing = isset($this->jsonResponse->IsErroredOnProcessing) ? $this->jsonResponse->IsErroredOnProcessing : null;
        $this->errorMessage = isset($this->jsonResponse->ErrorMessage) ? $this->jsonResponse->ErrorMessage : null;
        $this->errorDetails = isset($this->jsonResponse->ErrorDetails) ? $this->jsonResponse->ErrorDetails : null;
        $this->processingTime = isset($this->jsonResponse->ProcessingTimeInMilliseconds) ? $this->jsonResponse->ProcessingTimeInMilliseconds : null;
        $this->searchablePDFURL= isset($this->jsonResponse->SearchablePDFURL) ? $this->jsonResponse->SearchablePDFURL : null;
    }


    public function length()
    {
        if ($this->parsedResults == null) {
            return 0;
        }
        return count($this->parsedResults);
    }

    public function item($i)
    {
        if ($this->parsedResults == null || $i >= count($this->parsedResults)) {
            return null;
        }
        return new OcrResultItem($this->parsedResults[$i]);
    }

    public function exitCode()
    {
        return $this->ocrExitCode;
    }

    public function errorMessage()
    {
        return $this->errorMessage;
    }

    public function errorDetails()
    {
        return $this->errorDetails;
    }

    public function processingTime()
    {
        return $this->processingTime;
    }

    public function getJsonResponse() {
        return $this->jsonResponse;
    }
    public function __toString()
    {
        return 'OCR.space Response: (JSON)' . json_encode($this->jsonResponse);
    }
}
