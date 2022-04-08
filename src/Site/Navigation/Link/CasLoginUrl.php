<?php
namespace CAS\Site\Navigation\Link;

use Omeka\Api\Representation\SiteRepresentation;
use Omeka\Site\Navigation\Link\LinkInterface;
use Omeka\Stdlib\ErrorStore;

class CasLoginUrl implements LinkInterface
{
    protected $authenticationService;

    public function __construct($authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    public function getName()
    {
        return 'CAS Login URL'; // @translate
    }

    public function getFormTemplate()
    {
        return 'cas/common/navigation-link-form/cas-login-url';
    }

    public function isValid(array $data, ErrorStore $errorStore)
    {
        if (!isset($data['label']) || '' === trim($data['label'])) {
            $errorStore->addError('o:navigation', 'Invalid navigation: URL link missing label');
            return false;
        }

        return true;
    }

    public function getLabel(array $data, SiteRepresentation $site)
    {
        return isset($data['label']) && '' !== trim($data['label'])
            ? $data['label'] : null;
    }

    public function toZend(array $data, SiteRepresentation $site)
    {
        return [
            'route' => 'cas/login',
            'visible' => !$this->authenticationService->hasIdentity(),
        ];
    }

    public function toJstree(array $data, SiteRepresentation $site)
    {
        return [
            'label' => $data['label'],
        ];
    }
}
