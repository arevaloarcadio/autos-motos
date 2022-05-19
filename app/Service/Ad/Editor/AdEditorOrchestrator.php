<?php

declare(strict_types=1);

namespace App\Service\Ad\Editor;

use App\Enum\Core\ApprovalStatusEnum;
use App\Enum\User\RoleEnum;
use App\Exceptions\InvalidAdTypeProvidedException;
use App\Models\Ad;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Traversable;

/**
 * @package App\Service\Ad\Editor
 * @author  Dragos Becsan <dragosb@dreamlabs.ro>
 */
class AdEditorOrchestrator
{
    use InputFilterable;

    /**
     * @var IAdEditor[]
     */
    private $editors = [];

    public function __construct(Traversable $editors)
    {
        $this->editors = iterator_to_array($editors);
    }

    public function update(string $slug, array $input): Ad
    {
        $ad = $this->findAdForUpdate($slug);
        $ad = $this->updateAd($ad, $input);

        foreach ($this->getEditors() as $editor) {
            if (false === $editor->supports($ad->type)) {
                continue;
            }

            if (false === $this->hasInputForFillableFields(
                    $editor->getSpecificAdFromAd($ad)->getFillable(),
                    array_keys($input)
                )
            ) {
                return $ad;
            }

            return $editor->update($ad, $input);
        }

        throw new InvalidAdTypeProvidedException();
    }

    public function updateFull(string $adType, string $slug, array $input): Ad
    {
        $ad = $this->findAdForUpdate($slug);

        foreach ($this->getEditors() as $editor) {
            if (false === $editor->supports($adType)) {
                continue;
            }

            return $editor->updateFull($ad, $input);
        }

        throw new InvalidAdTypeProvidedException();
    }

    private function findAdForUpdate(string $slug): Ad
    {
        $query = Ad::whereSlug($slug);

        if (false === Auth::user()->hasRole(RoleEnum::ADMIN)) {
            $query->whereUserId(Auth::user()->id);
        }

        return $query->firstOrFail();
    }

    private function updateAd(Ad $ad, array $input): Ad
    {
        $relevantInput = $this->filterRelevantInput($ad, $input);
        if (0 === $relevantInput) {
            return $ad;
        }

        $ad->fill($relevantInput);

        if (true === $ad->isDirty()) {
            $ad->status = ApprovalStatusEnum::PENDING_APPROVAL;
            $ad->save();
        }

        return $ad;
    }

    /**
     * @param string[] $fillable
     * @param string[] $inputKeys
     *
     * @return bool
     */
    private function hasInputForFillableFields(array $fillable, array $inputKeys): bool
    {
        return count(array_intersect($fillable, $inputKeys)) > 0;
    }

    public function presentForm(string $adType, string $slug): View
    {
        $this->findAdForUpdate($slug);

        foreach ($this->getEditors() as $editor) {
            if (false === $editor->supports($adType)) {
                continue;
            }

            return $editor->presentForm($slug);
        }

        throw new InvalidAdTypeProvidedException();
    }

    /**
     * Get the value of the editors property.
     *
     * @return IAdEditor[]
     */
    public function getEditors(): array
    {
        return $this->editors;
    }
}
