<?php
/**
 * Dynamic return type for get_theme_mod()
 *
 * @package wpcore
 */

namespace wpcore\PHPStan;

use PhpParser\Node\Expr\FuncCall;
use PHPStan\Analyser\Scope;
use PHPStan\Reflection\FunctionReflection;
use PHPStan\Reflection\ParametersAcceptorSelector;
use PHPStan\Type\DynamicFunctionReturnTypeExtension;
use PHPStan\Type\MixedType;
use PHPStan\Type\StringType;
use PHPStan\Type\Type;
use PHPStan\Type\TypeCombinator;

/**
 * This class implements dynamic return types for wpcore specific theme mods.
 */
class GetThemeModReturnType implements DynamicFunctionReturnTypeExtension {

	/**
	 * wpcore specific theme modifications.
	 *
	 * @var array<int,string>
	 */
	private static $themeMods = [
		'wpcore_bootstrap_version',
		'wpcore_container_type',
		'wpcore_navbar_type',
		'wpcore_sidebar_position',
		'wpcore_site_info_override',
	];

	public function isFunctionSupported(FunctionReflection $functionReflection): bool
	{
		return $functionReflection->getName() === 'get_theme_mod';
	}

	public function getTypeFromFunctionCall(
		FunctionReflection $functionReflection,
		FuncCall $functionCall,
		Scope $scope
	): Type
	{
		$argType = $scope->getType($functionCall->getArgs()[0]->value);
		$defaultType = ParametersAcceptorSelector::selectFromArgs(
			$scope,
			$functionCall->getArgs(),
			$functionReflection->getVariants()
		)->getReturnType();

		if (count($argType->getConstantStrings()) === 0) {
			return null;
		}

		$returnType = [];
		foreach ($argType->getConstantStrings() as $constantString) {
			if (in_array($constantString->getValue(), self::$themeMods, true)) {
				$returnType[] = new StringType();
			} else {
				$returnType[] = $defaultType;
			}
		}
		$returnType = TypeCombinator::union(...$returnType);


		// Without second argument the default value is false, but can be filtered.
		$defaultType = new MixedType();
		if (count($functionCall->getArgs()) > 1) {
			$defaultType = $scope->getType($functionCall->getArgs()[1]->value);
		}

		return TypeCombinator::union($returnType, $defaultType);
	}
}
