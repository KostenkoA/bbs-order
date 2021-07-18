<?php

namespace App\Controller\Common;

use Symfony\Component\HttpFoundation\Request;

trait ProjectHeaderTrait
{
    protected function addProjectIfHeaderExist(Request $request): void
    {
        if ($project = $request->headers->get('Project')) {
            if ($request->isMethod(Request::METHOD_GET)) {
                $request->query->add(compact('project'));
            } else {
                $request->request->add(compact('project'));
            }
        }
    }

    /**
     * @param Request $request
     * @return string|null
     */
    private function getProjectName(Request $request): ?string
    {
        return $request->headers->get('Project');
    }
}
