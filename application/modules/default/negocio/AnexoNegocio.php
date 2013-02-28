<?php

/**
 * Ualison Aguiar
 *
 * @copyright Ualison Aguiar
 */

/**
 * Classe responsável pelas regras de negócio do anexo de arquivos
 *
 * @author Ualison Aguiar <ualison.aguiar@gmail.com>
 * @category Classe de teste
 * @since 1.0
 * @date 26/02/2012
 */
class NDefault_AnexoNegocio extends PDefault_Models_Anexo
{

    /**
     * Método que insere o arquivo no sistema
     *
     * @param type $file
     * @param type $dadoFile
     * @return void
     */
    public function uploadArquivo($file, $dadoFile)
    {
        if ( !$file['error'] ) {
            try {
                $nmArquivo = md5(mt_rand(1, 10000));
                $pathArquivo = PATH_FILE . '/' . $nmArquivo;
                //Fazendo o upload do arquivo
                if ( copy($file['tmp_name'], $pathArquivo) ) {
                    $dadoFile->caminho_file = $pathArquivo;
                    $dadoFile->extensao_file = self::tipoExtensao(
                            $dadoFile->type_file
                    );
                    $dadoFile->md5 = md5_file($pathArquivo);
                    return $this->insert((array) $dadoFile);
                } else {
                    throw new Zend_Exception(
                        'Não foi possível subir o arquivo.'
                    );
                }
            } catch ( Zend_Exception $exc ) {
                throw new Zend_Exception($exc->getMessage());
            }
        }
    }

    /**
     * Método responsável pela listagem das contas
     *
     * @param type $chvFile
     * @return ArrayIterator
     */
    public function listagem($chvFile = null)
    {
        $where = array();
        if ( !empty($chvFile) ) {
            $where['chv_file = ?'] = $chvFile;
        }
        return $this->fetchAll($where, 'dat_inclusao desc')->toArray();
    }

    /**
     * Método responsável pela exclusão do arquivo
     *
     * @param type $chvFile
     * @throws Zend_Exception
     * @return void
     */
    public function excluir($chvFile)
    {
        try {
            $coFile = $this->find($chvFile)->current();
            if ( count($coFile) == 0 ) {
                throw new Zend_Exception('Arquivo não localizado');
            }
            if ( !file_exists($coFile['caminho_file']) ) {
                $coFile->delete();
                throw new Zend_Exception('Arquivo não localizado');
            }
            unlink($coFile['caminho_file']);
            $coFile->delete();
        } catch ( Zend_Db_Exception $exc ) {
            throw new Zend_Exception($exc->getMessage());
        }
    }

    /**
     * Método que informar o tipo de extensão
     *
     * @param type $type
     * @return string
     */
    static function tipoExtensao($type)
    {
        $ext = '';
        switch ( $type ) {
            case 'application/pdf':
                $ext = 'pdf';
                break;
            case 'application/vnd.oasis.opendocument.text':
                $ext = 'odt';
                break;
            case 'image/jpeg':
                $ext = 'jpeg';
                break;
            default :
                $ext = '...';
                break;
        }
        return $ext;
    }
}