<?php
/**
 * @SWG\Swagger(
 *   @SWG\Info(
 *     version=SWAGGER_API_VERSION,
 *     title=SWAGGER_API_TITLE,
 *     description="Api Docs",
 *   ),
 *   schemes={"http"},
 *   basePath="/api",
 *   consumes={"application/json"},
 *   produces={"application/json"},
 *   @SWG\SecurityScheme(securityDefinition="basic", type="basic")
 * )
 */

/**
 * @SWG\Definition(definition="Id",       @SWG\Property(property="id",     type="integer", format="int32"),)
 * @SWG\Definition(definition="UserId",   @SWG\Property(property="user_id",type="integer", format="int32"),)
 * @SWG\Definition(definition="Email",    @SWG\Property(property="email",  type="string",  format="email", maxLength=255, example="example@example.com", description="must be a valid email address."),)
 * @SWG\Definition(definition="Phone",    @SWG\Property(property="phone",  type="string",  example="1,123456789", pattern="^\d{1,4},\d{5,10}$", description="[Validation Rule Details](https://regex101.com/r/iD1kP5/1), [Discussion](https://trello.com/c/n4Xqa2W5)"),)
 * @SWG\Definition(definition="Avatar",   @SWG\Property(property="avatar", type="string"),)
 * @SWG\Definition(definition="Cover",    @SWG\Property(property="cover",  type="string"),)
 * @SWG\Definition(definition="Nickname",    @SWG\Property(property="nickname",  type="string"),)
 * @SWG\Definition(definition="DateOfBirthday",    @SWG\Property(property="dt_birthday",  type="string"),)
 * @SWG\Definition(definition="FavoriteTeam",    @SWG\Property(property="favorite_team",  type="string"),)
 * @SWG\Definition(definition="PushNotification",    @SWG\Property(property="push_notification",  type="boolean"),)
 * @SWG\Definition(definition="ShowProfile",    @SWG\Property(property="show_profile",  type="boolean"),)
 */

/**
 *
 * @SWG\Definition(definition="Location",
 *   @SWG\Property(property="lat", type="number", format="float", minimum=-90, maximum=90, example=45.51695, description="min: -90f, max: 90f"),
 *   @SWG\Property(property="lng", type="number", format="float", minimum=-180, maximum=180, example=-73.55481, description="min: -180f, max: 180f"),
 * )
 */

/**
 * @SWG\Response(response="Ok", description="`OK`")),
 * @SWG\Response(response="Created", description="`CREATED`")),
 * @SWG\Response(response="BadRequest", description="`BAD_REQUEST`"),
 * @SWG\Response(response="Unauthorized", description="`UNAUTHORIZED`"),
 * @SWG\Response(response="NotFound", description="`NOT_FOUND`"),
 * @SWG\Response(response="ConflictEmailExists", description="`EMAIL_EXISTS`")),
 * @SWG\Response(response="Invalidation", description="`VALIDATION_ERROR`"),
 * @SWG\Response(response="UnexpectedError", description="`UNEXPECTED_ERROR`"),
 */


/**
 * @SWG\SecurityScheme(
 *   securityDefinition="X-Api-Token",
 *   type="apiKey",
 *   in="header",
 *   name="X-Api-Token"
 * )
 */