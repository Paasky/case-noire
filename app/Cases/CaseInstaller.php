<?php

namespace App\Cases;

use App\Constants\CaseConst;
use App\Models\CaseTemplate;
use App\Models\Clue;
use App\Models\Conversation;
use App\Models\ConversationLine;
use App\Models\Event;
use App\Models\Evidence;
use App\Models\Person;
use Illuminate\Database\Eloquent\Model;

abstract class CaseInstaller
{
    /** @var Clue[] */
    protected $clues = [];
    /** @var Evidence[] */
    protected $evidences = [];
    /** @var Person[] */
    protected $people = [];
    /** @var Conversation[] */
    protected $conversations = [];
    /** @var ConversationLine[] */
    protected $conversationLines = [];
    /** @var Event[] */
    protected $events = [];

    protected $conversationTree = [];

    protected $name = '';
    protected $description = '';
    protected $type = '';

    protected $createdModelIds = [];

    public static function install(): CaseTemplate
    {
        $case = static::init()->validate();

        if (CaseTemplate::whereName($case->name)->exists()) {
            throw new \Exception("CaseTemplate $case->name already exists");
        }

        $caseTemplate = CaseTemplate::create([
            'name' => $case->name,
            'description' => $case->description,
            'type' => $case->type,
        ]);

        $case
            ->createEvidences($caseTemplate)
            ->createPeople($caseTemplate)
            ->createEvents($caseTemplate)
            ->createClues($caseTemplate)
            ->createConversations($caseTemplate)
            ->createConversationLines($caseTemplate);

        return $caseTemplate;
    }
    
    protected static function init(): self 
    {
        Clue::unguard();
        Event::unguard();
        Evidence::unguard();
        Person::unguard();
        Conversation::unguard();
        ConversationLine::unguard();
        
        $case = new static();
        
        Clue::reguard();
        Event::reguard();
        Evidence::reguard();
        Person::reguard();
        Conversation::reguard();
        ConversationLine::reguard();
        
        return $case;
    }

    protected function createClues(CaseTemplate &$caseTemplate): self
    {
        foreach ($this->clues as $clue) {
            $fakeId = $clue->id;
            unset($clue['id']);
            $clue = $caseTemplate->clues()->save($clue);
            if ($fakeId) {
                $this->setCreatedModelId($clue, $fakeId);
            }
        }

        return $this;
    }

    protected function createConversations(CaseTemplate $caseTemplate): self
    {
        $conversationsToCreate = $this->conversations;
        while ($conversationsToCreate) {
            $somethingWasDone = false;

            foreach ($conversationsToCreate as $i => $conversation) {
                $modelId = $this->getCreatedModelId($conversation->model_type, $conversation->model_id);
                if (!$modelId) {
                    continue;
                }
                $conversation->model_id = $modelId;

                $fakeId = $conversation->id;
                unset($conversation['id']);
                /** @var Conversation $conversation */
                $conversation = $caseTemplate->conversations()->save($conversation);
                if ($fakeId) {
                    $this->setCreatedModelId($conversation, $fakeId);
                }
                unset($conversationsToCreate[$i]);
                $somethingWasDone = true;
            }

            if (!$somethingWasDone) {
                throw new \Exception("Invalid Case $this->name, could not create any conversations from " . json_encode($conversationsToCreate));
            }
        }

        return $this;
    }

    protected function createConversationLines(CaseTemplate $caseTemplate): self
    {
        $linesToCreate = $this->conversationLines;
        while ($linesToCreate) {
            $somethingWasDone = false;

            foreach ($linesToCreate as $i => $line) {

                // 1) If the line comes after another line, find the created Line ID
                $fakeFromLineId = $line->from_line_id;
                if ($fakeFromLineId) {
                    $fromLineId = $this->getCreatedModelId(ConversationLine::class, $fakeFromLineId);
                    if (!$fromLineId) {
                        continue;
                    }
                    $line->from_line_id = $fromLineId;
                } else {
                    $fromLineId = null;
                }

                // 2) Conversation must be created
                $conversationId = $this->getCreatedModelId(Conversation::class, $line->conversation_id);
                if (!$conversationId) {
                    throw new \Exception("Invalid Case $this->name, ConversationLine[$i] requires Conversation that has not been created");
                }
                $line->conversation_id = $conversationId;

                // 3) If the line is said by a person, find the created Person ID
                $fakeSaidById = is_numeric($line->said_by) ? $line->said_by : null;
                if ($fakeSaidById) {
                    $saidById = $this->getCreatedModelId(Person::class, $fakeSaidById);
                    if (!$saidById) {
                        throw new \Exception("Invalid Case $this->name, ConversationLine[$i] requires Person that has not been created");
                    }
                    $line->said_by = $saidById;
                }

                // 4) Create the line
                $fakeId = $line->id;
                unset($line['id']);

                $line = ConversationLine::create($line->toArray());
                if ($fakeId) {
                    $this->setCreatedModelId($line, $fakeId);
                }
                unset($linesToCreate[$i]);
                $somethingWasDone = true;
            }

            if (!$somethingWasDone) {
                throw new \Exception("Invalid Case $this->name, could not create any ConversationLines from " . json_encode($linesToCreate));
            }
        }

        return $this;
    }

    protected function createEvidences(CaseTemplate &$caseTemplate): self
    {
        foreach ($this->evidences as $evidence) {
            $fakeId = $evidence->id;
            unset($evidence['id']);
            $evidence = $caseTemplate->evidences()->save($evidence);
            if ($fakeId) {
                $this->setCreatedModelId($evidence, $fakeId);
            }
        }

        return $this;
    }

    protected function createEvents(CaseTemplate &$caseTemplate): self
    {
        $eventsToCreate = $this->events;
        while ($eventsToCreate) {
            $somethingWasDone = false;

            foreach ($eventsToCreate as $i => $event) {
                $fakeFiredById = $event->fired_by_event_id;
                if ($fakeFiredById) {
                    $firedById = $this->getCreatedModelId(Event::class, $fakeFiredById);
                    if (!$firedById) {
                        continue;
                    }
                    $event->fired_by_event_id = $firedById;
                }

                $fakeId = $event->id;
                unset($event['id']);
                /** @var Event $event */
                $event = $caseTemplate->events()->save($event);
                if ($fakeId) {
                    $this->setCreatedModelId($event, $fakeId);
                }
                unset($eventsToCreate[$i]);
                $somethingWasDone = true;
            }

            if (!$somethingWasDone) {
                throw new \Exception("Invalid Case $this->name, could not create any events from " . json_encode($eventsToCreate));
            }
        }

        return $this;
    }

    protected function createPeople(CaseTemplate &$caseTemplate): self
    {
        foreach ($this->people as $person) {
            $fakeId = $person->id;
            unset($person['id']);
            $person = $caseTemplate->people()->save($person);
            if ($fakeId) {
                $this->setCreatedModelId($person, $fakeId);
            }
        }

        return $this;
    }

    protected function getCreatedModelId($class, $fakeId): ?int
    {
        if (!is_string($class)) {
            $class = get_class($class);
        }
        return $this->createdModelIds[$class][$fakeId] ?? null;
    }

    protected function setCreatedModelId(Model $model, $fakeId): self
    {
        $this->createdModelIds[get_class($model)][$fakeId] = $model->id;
        return $this;
    }

    public function validate(): self
    {
        $errors = [];
        
        if (!$this->name) {
            $errors[] = 'Name is missing';
        }

        if (!$this->description) {
            $errors[] = 'Description is missing';
        }

        if (!$this->type) {
            $errors[] = 'Type is missing';
        } elseif (!in_array($this->type, CaseConst::TYPES)) {
            $errors[] = "Type $this->type is invalid";
        }

        if (!$this->clues) {
            $errors[] = 'Clues is empty';
        }
        foreach ($this->clues as $i => $item) {
            if (!$item instanceof Clue) {
                $errors[] = "Clues[$i] is not a Clue";
            }
        }
        
        if (!$this->evidences) {
            $errors[] = 'Evidences is empty';
        }
        foreach ($this->evidences as $i => $item) {
            if (!$item instanceof Evidence) {
                $errors[] = "Evidences[$i] is not an Evidence";
            }
        }
        
        if (!$this->people) {
            $errors[] = 'People is empty';
        }
        foreach ($this->people as $i => $item) {
            if (!$item instanceof Person) {
                $errors[] = "People[$i] is not a Person";
            }
        }
        
        if (!$this->conversations) {
            $errors[] = 'Conversations is empty';
        }
        if (!$this->conversationTree) {
            $errors[] = 'ConversationTree is empty';
        }
        foreach ($this->conversations as $i => $item) {
            if (!$item instanceof Conversation) {
                $errors[] = "Conversations[$i] is not a Conversation";
            }
        }
        
        foreach ($this->events as $i => $item) {
            if (!$item instanceof Event) {
                $errors[] = "Events[$i] is not an Event";
            }
        }

        if ($errors) {
            throw new \Exception('Invalid Case: ' . json_encode($errors));
        }

        return $this;
    }
}