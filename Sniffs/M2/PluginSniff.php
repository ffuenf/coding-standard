<?php

class Ecg_Sniffs_M2_PluginSniff implements PHP_CodeSniffer_Sniff
{

    const PARAMS_QTY = 2;

    protected $prefixes = array(
        'before',
        'after',
        'around'
    );

    protected $exclude = array(
        /*'beforeSave',
        'afterSave',
        'aroundSave',*/
    );


    public function register()
    {
        return array(
            T_FUNCTION
        );
    }


    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $functionName = $phpcsFile->getDeclarationName($stackPtr);

        if ($this->startsWith($functionName, $this->prefixes, $this->exclude)) {
            $paramsQty = count($phpcsFile->getMethodParameters($stackPtr));
            if ($paramsQty < self::PARAMS_QTY) {
                $phpcsFile->addWarning('Plugin '.$functionName.' function must have at least two parameters.', $stackPtr);
            }

            $tokens = $phpcsFile->getTokens();

            $hasReturn = false;
            foreach ($tokens as $currToken) {
                if ($currToken['code'] == T_RETURN && isset($currToken['conditions'][$stackPtr])) {
                    $hasReturn = true;
                    break;
                }
            }

            if (!$hasReturn) {
                $phpcsFile->addError('Plugin '.$functionName.' function must return value.', $stackPtr);
            }
        }
    }


    protected function startsWith($haystack, array $needle, array $excludeFunctions = array())
    {
        if (in_array($haystack, $excludeFunctions)) {
            return false;
        }
        $haystackLength = strlen($haystack);
        foreach ($needle as $currPref) {
            $length = strlen($currPref);
            if ($haystackLength != $length && substr($haystack, 0, $length) === $currPref) {
                return true;
            }
        }
        return false;
    }
}
