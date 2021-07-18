<?php

namespace App\Component\UserService\Request;

use App\Component\UserService\UserServiceException;
use App\Interfaces\HttpRequestInterface;
use Exception;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;

abstract class RequestAbstract extends \App\Component\RequestAbstract
{
    /**
     * @return void
     * @throws UserServiceException
     */
    public function send(): void
    {
        try {
            parent::send();
        } catch (ConnectException $exception) {
            throw new UserServiceException($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        } catch (RequestException $exception) {
            $this->response = $exception->getResponse();
        } catch (GuzzleException | Exception $exception) {
            throw new UserServiceException($exception->getMessage(), $exception->getCode(), $exception->getPrevious());
        }
    }
}
