<?php
namespace TelegramApiParser\CodeGenerator\NewPHP\PreparePipelineMethods;

use Appto\TelegramBot\Support\Resolvers\ChatMemberResolve;

class ChatMemberUpdated
{
    /**
     * for: old_chat_member (ChatMember)
     */
    public function __invoke(array $properties): array
    {
        $resolver = new ChatMemberResolve();

        if (isset($properties['old_chat_member']) || $properties['old_chat_member']) {
            $properties['old_chat_member'] = $resolver->resolve($properties['old_chat_member']);
        }

        if (isset($properties['new_chat_member']) || $properties['new_chat_member']) {
            $properties['new_chat_member'] = $resolver->resolve($properties['new_chat_member']);
        }


        return $properties;
    }
}
