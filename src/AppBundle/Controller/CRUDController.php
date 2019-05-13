<?php

namespace AppBundle\Controller;

use Sonata\AdminBundle\Exception\ModelManagerException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sonata\AdminBundle\Controller\CRUDController as Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;

class CRUDController extends Controller
{

        public function deleteAction($id)
        {

//            throw new \PDOException('nbv');

            $request = $this->getRequest();
            $id = $request->get($this->admin->getIdParameter());
            $object = $this->admin->getObject($id);

            if (!$object) {
                throw $this->createNotFoundException(sprintf('unable to find the object with id: %s', $id));
            }

            $this->admin->checkAccess('delete', $object);

            $preResponse = $this->preDelete($request, $object);

            if ($preResponse !== null) {
                return $preResponse;
            }

            if ($this->getRestMethod() == 'DELETE') {
                // check the csrf token
                $this->validateCsrfToken('sonata.delete');

                $objectName = $this->admin->toString($object);



                try {

                    $this->admin->delete($object);

                    if ($this->isXmlHttpRequest()) {
                        return $this->renderJson(array('result' => 'ok'), 200, array());
                    }

                    $this->addFlash(
                        'sonata_flash_success',
                        $this->trans(
                            'flash_delete_success',
                            array('%name%' => $this->escapeHtml($objectName)),
                            'SonataAdminBundle'
                        )
                    );

                } catch (ModelManagerException $e) {


                    $this->handleModelManagerException($e);

                    if ($this->isXmlHttpRequest()) {
                        return $this->renderJson(array('result' => 'error'), 200, array());
                    }

                    $this->addFlash(
                        'sonata_flash_error',
                        $this->trans(
                            'flash_delete_error',
                            array('%name%' => $this->escapeHtml($objectName)),
                            'SonataAdminBundle'
                        )
                    );
                }


                return $this->redirectTo($object);
            }

            return $this->render($this->admin->getTemplate('delete'), array(
                'object' => $object,
                'action' => 'delete',
                'csrf_token' => $this->getCsrfToken('sonata.delete'),
            ), null);
        }


}